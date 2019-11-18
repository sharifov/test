<?php

namespace sales\services\lead\qcall;

use common\models\Call;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Lead;
use sales\repositories\lead\LeadFlowRepository;
use sales\repositories\lead\LeadQcallRepository;
use Yii;
use common\models\LeadQcall;
use common\models\QcallConfig;
use yii\db\ActiveQuery;
use yii\helpers\VarDumper;

/**
 * Class QCallService
 *
 * @property LeadQcallRepository $repository
 * @property LeadFlowRepository $leadFlowRepository
 */
class QCallService
{
    private $repository;
    private $leadFlowRepository;

    /**
     * @param LeadQcallRepository $repository
     * @param LeadFlowRepository $leadFlowRepository
     */
    public function __construct(
        LeadQcallRepository $repository,
        LeadFlowRepository $leadFlowRepository
    )
    {
        $this->repository = $repository;
        $this->leadFlowRepository = $leadFlowRepository;
    }

    public function test()
    {
        $findPhoneParams = new FindPhoneParams(6,1);
        $query = $this->getDepartmentRedialPhonesQuery($findPhoneParams);
        $dump = $this->findPhoneWithMinimumAttemptsByDate($query);
        VarDumper::dump($dump);
    }

    public function createOrUpdate(Lead $lead): void
    {
        if ($lq = $lead->leadQcall) {
            $this->updateInterval(
                $lq,
                new Config($lead->status,
                    $lead->getCountOutCallsLastFlow()),
                $lead->offset_gmt,
                new FindPhoneParams($lead->project_id, $lead->l_dep_id, $lead->id)
            );

        } else {
            $this->create(
                $lead->id,
                new Config($lead->status, $lead->getCountOutCallsLastFlow()),
                ($lead->project_id * 10),
                $lead->offset_gmt,
                new FindPhoneParams($lead->project_id, $lead->l_dep_id)
            );
        }
    }

    /**
     * @param int $leadId
     * @param Config $config
     * @param int $weight
     * @param string|null $clientGmt
     * @param FindPhoneParams $findPhoneParams
     */
    public function create(int $leadId, Config $config, int $weight, ?string $clientGmt, FindPhoneParams $findPhoneParams): void
    {
        if (!$qConfig = $this->findConfig($config)) {
            Yii::warning('QCallService:create. Config not found for status: ' . $config->status . ', callCount: ' . $config->callCount);
            return;
        }

        if ($this->isExists($leadId)) {
            Yii::error('QCallService:create. LeadId: ' . $leadId . ' is exists');
            return;
        }

        $interval = (new CalculateDateService())->calculate(
            $qConfig->qc_time_from,
            $qConfig->qc_time_to,
            $qConfig->qc_client_time_enable,
            $clientGmt,
            'now'
        );

        $phone = $this->findPhone(null, $qConfig->qc_phone_switch, $findPhoneParams);

        $qCall = LeadQcall::create($leadId, $weight, $interval, $phone);

        $this->repository->save($qCall);
    }

    /**
     * @param LeadQcall $qCall
     * @param Config $config
     * @param string|null $clientGmt
     * @param FindPhoneParams $findPhoneParams
     */
    public function updateInterval(LeadQcall $qCall, Config $config, ?string $clientGmt, FindPhoneParams $findPhoneParams): void
    {
        if (!$qConfig = $this->findConfig($config)) {
            Yii::warning('QCallService:updateInterval. Config not found for status: ' . $config->status . ', callCount: ' . $config->callCount);
            return;
        }

        $interval = (new CalculateDateService())->calculate(
            $qConfig->qc_time_from,
            $qConfig->qc_time_to,
            $qConfig->qc_client_time_enable,
            $clientGmt,
            'now'
        );

        $qCall->updateInterval($interval);

        $phone = $this->findPhone($qCall->lqc_call_from, $qConfig->qc_phone_switch, $findPhoneParams);

        $qCall->updateCallFrom($phone);

        $this->repository->save($qCall);
    }

    /**
     * @param int $leadId
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(int $leadId): void
    {
        if (!$qCall = $this->findQcall($leadId)) {
//            Yii::warning('QCallService:remove. Not found leadId: ' . $leadId);
            return;
        }
        $this->repository->remove($qCall);
    }

    public function resetAttempts(Lead $lead): void
    {
        if ($lastLeadFlow = $lead->lastLeadFlow) {
            $lastLeadFlow->resetAttempts();
            $this->leadFlowRepository->save($lastLeadFlow);
        }
    }

    /**
     * @param string|null $callFrom
     * @param $phoneSwitch
     * @param FindPhoneParams $findPhoneParams
     * @return string|null
     */
    private function findPhone(?string $callFrom, $phoneSwitch, FindPhoneParams $findPhoneParams):? string
    {
        $phonesQuery = $this->getDepartmentRedialPhonesQuery($findPhoneParams);
        $clone = clone $phonesQuery;
        $count = (int)$clone->count();

        if ($count === 0) {
            return null;
        }

        if ($count === 1) {
            return ($phonesQuery->one())->dpp_phone_number;
        }

        if ($callFrom === null) {
            return $this->findPhoneWithMinimumAttemptsByDate($phonesQuery);
        }

        if ($callFrom !== null && $phoneSwitch) {
            return $this->findPhoneWithMinimumAttemptsByLead($phonesQuery, $findPhoneParams->leadId);
        }

        return $callFrom;
    }

    /**
     * @param ActiveQuery $phonesQuery
     * @param int|null $leadId
     * @return string|null
     */
    private function findPhoneWithMinimumAttemptsByLead(ActiveQuery $phonesQuery, ?int $leadId):? string
    {
        if ($leadId === null) {
            return null;
        }

        $call = Call::find()
            ->select(['c_from', 'count_calls' => 'count(*)'])
            ->andWhere(['c_from' => $phonesQuery])
            ->andWhere(['c_call_type_id' => Call::CALL_TYPE_OUT])
            ->andWhere(['c_lead_id' => $leadId])
            ->groupBy(['c_from'])
            ->orderBy(['count_calls' => SORT_ASC])
            ->limit(1)
            ->one();

        if ($call) {
            return $call->c_from;
        }

        return $this->getDefaultPhone($phonesQuery);
    }

    /**
     * @param ActiveQuery $phonesQuery
     * @return string|null
     */
    private function findPhoneWithMinimumAttemptsByDate(ActiveQuery $phonesQuery):? string
    {
        $hours = (int)Yii::$app->params['settings']['redial_default_phone_history_hours'];
        $interval = new \DateInterval('PT' . $hours . 'H');
        $interval->invert = 1;
        $dt = (new \DateTime('now', new \DateTimeZone('UTC')))->add($interval);

        $call = Call::find()
            ->select(['c_from', 'count_calls' => 'count(*)'])
            ->andWhere(['c_from' => $phonesQuery])
            ->andWhere(['c_call_type_id' => Call::CALL_TYPE_OUT])
            ->andWhere(['>', 'c_created_dt', $dt->format('Y-m-d H:i:s')])
            ->groupBy(['c_from'])
            ->orderBy(['count_calls' => SORT_ASC])
            ->limit(1)
            ->one();

        if ($call) {
            return $call->c_from;
        }

        return $this->getDefaultPhone($phonesQuery);
    }

    /**
     * @param ActiveQuery $phonesQuery
     * @return string|null
     */
    private function getDefaultPhone(ActiveQuery $phonesQuery):? string
    {
        /** @var DepartmentPhoneProject $phone */
        if ($phone = $phonesQuery->limit(1)->one()) {
            return $phone->dpp_phone_number;
        }
        return null;
    }

    /**
     * @param FindPhoneParams $findPhoneParams
     * @return ActiveQuery
     */
    private function getDepartmentRedialPhonesQuery(FindPhoneParams $findPhoneParams): ActiveQuery
    {
        $query = DepartmentPhoneProject::find()
            ->select(['dpp_phone_number'])
            ->andWhere(['dpp_project_id' => $findPhoneParams->projectId])
            ->andWhere(['dpp_redial' => true])
            ->andWhere(['IS NOT', 'dpp_phone_number', null]);

        if ($findPhoneParams->departmentId === null) {
            $query->andWhere(['or',
                ['dpp_dep_id' => Department::DEPARTMENT_SALES],
                ['IS', 'dpp_dep_id', NULL]
            ]);
        } else {
            $query->andWhere(['dpp_dep_id' => $findPhoneParams->departmentId]);
        }

        return $query;
    }

    /**
     * @param int $leadId
     * @return LeadQcall|null
     */
    private function findQcall(int $leadId):? LeadQcall
    {
        return LeadQcall::find()->andWhere(['lqc_lead_id' => $leadId])->one();
    }

    /**
     * @param Config $config
     * @return QcallConfig|null
     */
    private function findConfig(Config $config):? QcallConfig
    {
        return QcallConfig::find()->where(['qc_status_id' => $config->status])
            ->andWhere(['<=', 'qc_call_att', $config->callCount])
            ->orderBy(['qc_call_att' => SORT_DESC])->limit(1)->one();
    }

    /**
     * @param int $leadId
     * @return bool
     */
    private function isExists(int $leadId): bool
    {
        return LeadQcall::find()->andWhere(['lqc_lead_id' => $leadId])->exists();
    }
}
