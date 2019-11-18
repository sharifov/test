<?php

namespace sales\services\lead\qcall;

use common\models\Call;
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
 * @property LeadQcallRepository $leadQcallRepository
 * @property LeadFlowRepository $leadFlowRepository
 */
class QCallService
{
    private $leadQcallRepository;
    private $leadFlowRepository;

    /**
     * @param LeadQcallRepository $leadQcallRepository
     * @param LeadFlowRepository $leadFlowRepository
     */
    public function __construct(
        LeadQcallRepository $leadQcallRepository,
        LeadFlowRepository $leadFlowRepository
    )
    {
        $this->leadQcallRepository = $leadQcallRepository;
        $this->leadFlowRepository = $leadFlowRepository;
    }

    /**
     * @param Lead $lead
     */
    public function createOrUpdate(Lead $lead): void
    {
        if ($lq = $lead->leadQcall) {
            $this->updateInterval(
                $lq,
                new Config($lead->status, $lead->getCountOutCallsLastFlow()),
                $lead->offset_gmt,
                new FindPhoneParams($lead->project_id, $lead->l_dep_id)
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
     * @param string|null $phoneFrom
     */
    public function create(
        int $leadId,
        Config $config,
        int $weight,
        ?string $clientGmt,
        FindPhoneParams $findPhoneParams,
        ?string $phoneFrom = null
    ): void
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

        $phone = $phoneFrom ?: $this->findPhone(null, $qConfig->qc_phone_switch, $findPhoneParams);

        $qCall = LeadQcall::create($leadId, $weight, $interval, $phone);

        $this->leadQcallRepository->save($qCall);
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

        $phone = $this->findPhone($qCall->lqc_call_from, $qConfig->qc_phone_switch, $findPhoneParams, $qCall->lqc_lead_id);

        $qCall->updateCallFrom($phone);

        $this->leadQcallRepository->save($qCall);
    }

    /**
     * @param LeadQcall $qCall
     * @param Config $config
     * @param FindPhoneParams $findPhoneParams
     * @return string|null
     */
    public function updateCallFrom(LeadQcall $qCall, Config $config, FindPhoneParams $findPhoneParams):? string
    {
        if (!$qConfig = $this->findConfig($config)) {
            Yii::warning('QCallService:updateCallFrom. Config not found for status: ' . $config->status . ', callCount: ' . $config->callCount);
            return null;
        }

        $phone = $this->findPhone(null, $qConfig->qc_phone_switch, $findPhoneParams);

        $qCall->updateCallFrom($phone);

        $this->leadQcallRepository->save($qCall);

        return $phone;
    }

    public function reservation(LeadQcall $qCall): void
    {
        $seconds = (int)Yii::$app->params['settings']['redial_reservation_time'];
        $dt = (new \DateTime('now', new \DateTimeZone('UTC')))->add(new \DateInterval('PT' . $seconds . 'S'));
        $qCall->updateReservationTime($dt);
        $this->leadQcallRepository->save($qCall);
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
        $this->leadQcallRepository->remove($qCall);
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
     * @param int|null $leadId
     * @return string|null
     */
    private function findPhone(?string $callFrom, $phoneSwitch, FindPhoneParams $findPhoneParams, ?int $leadId = null):? string
    {
        $phonesQuery = DepartmentPhoneProject::find()->redialPhones($findPhoneParams->projectId, $findPhoneParams->departmentId);
        $clone = clone $phonesQuery;
        $count = (int)$clone->count();

        if ($count === 0) {
            return null;
        }

        if ($count === 1) {
            return ($phonesQuery->asArray()->one())['dpp_phone_number'];
        }

        if ($callFrom === null) {
            return $this->findPhoneWithMinimumAttemptsByDate($phonesQuery);
        }

        if ($callFrom !== null && $phoneSwitch) {
            return $this->findPhoneWithMinimumAttemptsByLead($phonesQuery, $leadId);
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
            ->asArray()
            ->limit(1)
            ->one();

        if ($call) {
            return $call['c_from'];
        }

        return $this->getFirstPhone($phonesQuery);
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
            ->asArray()
            ->limit(1)
            ->one();

        if ($call) {
            return $call['c_from'];
        }

        return $this->getFirstPhone($phonesQuery);
    }

    /**
     * @param ActiveQuery $phonesQuery
     * @return string|null
     */
    private function getFirstPhone(ActiveQuery $phonesQuery):? string
    {
        /** @var DepartmentPhoneProject $phone */
        if ($phone = $phonesQuery->limit(1)->one()) {
            return $phone->dpp_phone_number;
        }
        return null;
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
        return QcallConfig::find()->config($config->status, $config->callCount);
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
