<?php

namespace src\services\lead\qcall;

use common\models\Call;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\PhoneBlacklist;
use common\models\query\DepartmentPhoneProjectQuery;
use common\models\Lead;
use common\models\ProjectWeight;
use common\models\StatusWeight;
use src\helpers\setting\SettingHelper;
use src\model\leadRedial\queue\ClientPhones;
use src\model\leadRedial\job\LeadRedialAssignToUsersJob;
use src\model\phoneNumberRedial\entity\Scopes\PhoneNumberRedialQuery;
use src\repositories\lead\LeadFlowRepository;
use src\repositories\lead\LeadQcallRepository;
use Yii;
use common\models\LeadQcall;
use common\models\QcallConfig;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 * Class QCallService
 *
 * @property LeadQcallRepository $leadQcallRepository
 * @property LeadFlowRepository $leadFlowRepository
 * @property ClientPhones $clientPhones
 */
class QCallService
{
    private $leadQcallRepository;
    private $leadFlowRepository;
    private ClientPhones $clientPhones;

    public function __construct(
        LeadQcallRepository $leadQcallRepository,
        LeadFlowRepository $leadFlowRepository,
        ClientPhones $clientPhones
    ) {
        $this->leadQcallRepository = $leadQcallRepository;
        $this->leadFlowRepository = $leadFlowRepository;
        $this->clientPhones = $clientPhones;
    }

    public function createByDefault(Lead $lead): ?int
    {
        return $this->create(
            $lead->id,
            new Config($lead->status, $lead->getCountOutCallsLastFlow()),
            new FindWeightParams($lead->project_id, $lead->status),
            $lead->offset_gmt,
            new FindPhoneParams($lead->project_id, $lead->l_dep_id)
        );
    }

    /**
     * @param int $leadId
     * @param Config $config
     * @param FindWeightParams $findWeightParams
     * @param string|null $clientGmt
     * @param FindPhoneParams $findPhoneParams
     * @param string|null $phoneFrom
     * @return int|null
     */
    public function create(
        int $leadId,
        Config $config,
        FindWeightParams $findWeightParams,
        ?string $clientGmt,
        FindPhoneParams $findPhoneParams,
        ?string $phoneFrom = null
    ): ?int {
        if (!$qConfig = $this->findConfig($config)) {
            //Yii::warning('QCallService:create. Config not found for status: ' . $config->status . ', callCount: ' . $config->callCount);
            return null;
        }

        if ($this->isExist($leadId)) {
            Yii::error('QCallService:create. LeadId: ' . $leadId . ' is exists');
            return null;
        }

        $lead = Lead::findOne($leadId);
        if ($lead) {
            $clientPhone = $this->clientPhones->getFirstClientPhone($lead);
            if ($clientPhone && PhoneBlacklist::find()->isExists($clientPhone->phone)) {
                Yii::warning([
                    'message' => 'Lead not added to Lead Redial Queue, because client phone is blocked.',
                    'leadId' => $lead->id,
                    'phone' => $clientPhone->phone,
                ], 'QCallService:create');
                return null;
            }
        }

        $weight = $this->findWeight($findWeightParams);

        $interval = (new CalculateDateService())->calculate(
            $qConfig->qc_time_from,
            $qConfig->qc_time_to,
            $qConfig->qc_client_time_enable,
            $clientGmt,
            'now'
        );

        if (!empty($clientPhone) && SettingHelper::leadRedialEnabled() && SettingHelper::isPhoneNumberRedialEnabled()) {
            $phoneNumberRedial = PhoneNumberRedialQuery::getOneMatchingByClientPhone($clientPhone->phone);
            if ($phoneNumberRedial) {
                $phoneFrom = $phoneNumberRedial->phoneList->pl_phone_number;
            }
        }

        $phone = $phoneFrom ?: $this->findPhone(null, $qConfig->qc_phone_switch, $findPhoneParams);

        $qCall = LeadQcall::create($leadId, $weight, $interval, $phone);

        $this->leadQcallRepository->save($qCall);

        if (SettingHelper::leadRedialEnabled()) {
            \Yii::$app->queue_lead_redial->priority(1)->push(new LeadRedialAssignToUsersJob($qCall->lqc_lead_id));
        }

        return $qCall->lqc_lead_id;
    }

    /**
     * @param LeadQcall $qCall
     * @param Config $config
     * @param string|null $clientGmt
     * @param FindPhoneParams $findPhoneParams
     * @param FindWeightParams $findWeightParams
     */
    public function updateInterval(
        LeadQcall $qCall,
        Config $config,
        ?string $clientGmt,
        FindPhoneParams $findPhoneParams,
        FindWeightParams $findWeightParams
    ): void {
        if (!$qConfig = $this->findConfig($config)) {
//            Yii::warning('QCallService:updateInterval. Config not found for status: ' . $config->status . ', callCount: ' . $config->callCount);
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

        $qCall->updateWeight($this->findWeight($findWeightParams));

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
    public function updateCallFrom(LeadQcall $qCall, Config $config, FindPhoneParams $findPhoneParams): ?string
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

    /**
     * @param LeadQcall $qCall
     * @param int $userId
     */
    public function reservation(LeadQcall $qCall, int $userId): void
    {
        $seconds = (int)Yii::$app->params['settings']['redial_reservation_time'];
        $dt = (new \DateTime('now', new \DateTimeZone('UTC')))->add(new \DateInterval('PT' . $seconds . 'S'));
        $qCall->reservation($dt, $userId);
        $this->leadQcallRepository->save($qCall);
    }

    /**
     * @param LeadQcall $qCall
     */
    public function resetReservation(LeadQcall $qCall): void
    {
        $minutes = (int)Yii::$app->params['settings']['redial_failed_time_difference'];
        $qCall->reservation((new \DateTime('now', new \DateTimeZone('UTC')))->add(new \DateInterval('PT' . $minutes . 'M')), null);
//        $qCall->updateInterval(new Interval(
//            (new \DateTimeImmutable($qCall->lqc_dt_from))->add(new \DateInterval('PT' . $minutes . 'M')),
//            new \DateTimeImmutable($qCall->lqc_dt_to)
//        ));
        $this->leadQcallRepository->save($qCall);
    }

    /**
     * @param int $userId
     */
    public function resetOldReservationByUser(int $userId): void
    {
        $qCalls = LeadQcall::find()->andWhere(['lqc_reservation_user_id' => $userId])->all();
        foreach ($qCalls as $qCall) {
            $qCall->resetReservation();
            $this->leadQcallRepository->save($qCall);
        }
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
    public function findPhone(?string $callFrom, $phoneSwitch, FindPhoneParams $findPhoneParams, ?int $leadId = null): ?string
    {
        $phonesQuery = DepartmentPhoneProject::find()//->redialPhones($findPhoneParams->projectId, $findPhoneParams->departmentId);
//            ->select(['dpp_phone_number'])
            ->select(['pl_phone_number'])
            ->innerJoinWith('phoneList', false)
            ->enabled()
            ->redial()
            ->byProject($findPhoneParams->projectId)
            //->andWhere(['IS NOT', 'dpp_phone_number', null])
            ->byDepartment($findPhoneParams->departmentId ?: Department::DEPARTMENT_SALES);

        $clone = clone $phonesQuery;
        $count = (int)$clone->count();

        if ($count === 0) {
            return null;
        }

        if ($count === 1) {
//            return ($phonesQuery->asArray()->one())['dpp_phone_number'];
            return ($phonesQuery->asArray()->one())['pl_phone_number'];
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
     * @param DepartmentPhoneProjectQuery $phonesQuery
     * @param int|null $leadId
     * @return string|null
     */
    private function findPhoneWithMinimumAttemptsByLead(DepartmentPhoneProjectQuery $phonesQuery, ?int $leadId): ?string
    {
        if ($leadId === null) {
            return null;
        }

        $firstPhoneClone = clone $phonesQuery;

        $call = $phonesQuery
            ->addSelect(['count_calls' =>
                (new Query())
                    ->from(Call::tableName())
                    ->select(['count(*)'])
//                    ->andWhere('c_from = dpp_phone_number')
                    ->andWhere('c_from = pl_phone_number')
                    ->andWhere(['c_call_type_id' => Call::CALL_TYPE_OUT])
                    ->andWhere(['c_lead_id' => $leadId])
            ])
            ->orderBy(['count_calls' => SORT_ASC])
            ->asArray()
            ->limit(1)
            ->one();

        if ($call) {
//            return $call['dpp_phone_number'];
            return $call['pl_phone_number'];
        }

        return $this->getFirstPhone($firstPhoneClone);
    }

    private function findPhoneWithMinimumAttemptsByDate(DepartmentPhoneProjectQuery $phonesQuery): ?string
    {
        $hours = (int)Yii::$app->params['settings']['redial_default_phone_history_hours'];
        $interval = new \DateInterval('PT' . $hours . 'H');
        $interval->invert = 1;
        $dt = (new \DateTime('now', new \DateTimeZone('UTC')))->add($interval);

        $firstPhoneClone = clone $phonesQuery;

        $call = $phonesQuery
            ->addSelect(['count_calls' =>
                (new Query())
                    ->from(Call::tableName())
                    ->select(['count(*)'])
//                    ->andWhere('c_from = dpp_phone_number')
                    ->andWhere('c_from = pl_phone_number')
                    ->andWhere(['c_call_type_id' => Call::CALL_TYPE_OUT])
                    ->andWhere(['>', 'c_created_dt', $dt->format('Y-m-d H:i:s')])
            ])
            ->orderBy(['count_calls' => SORT_ASC])
            ->asArray()
            ->limit(1)
            ->one();

        if ($call) {
//            return $call['dpp_phone_number'];
            return $call['pl_phone_number'];
        }

        return $this->getFirstPhone($firstPhoneClone);
    }

    /**
     * @param DepartmentPhoneProjectQuery $phonesQuery
     * @return string|null
     */
    private function getFirstPhone(DepartmentPhoneProjectQuery $phonesQuery): ?string
    {
        /** @var DepartmentPhoneProject $phone */
        if ($phone = $phonesQuery->limit(1)->asArray()->one()) {
            return $phone['pl_phone_number'];
        }
        return null;
    }

    /**
     * @param int $leadId
     * @return LeadQcall|null
     */
    public function findQcall(int $leadId): ?LeadQcall
    {
        return LeadQcall::find()->andWhere(['lqc_lead_id' => $leadId])->one();
    }

    /**
     * @param Config $config
     * @return QcallConfig|null
     */
    public function findConfig(Config $config): ?QcallConfig
    {
        return QcallConfig::find()->config($config->status, $config->callCount);
    }

    /**
     * @param int $leadId
     * @return bool
     */
    public function isExist(int $leadId): bool
    {
        return LeadQcall::find()->andWhere(['lqc_lead_id' => $leadId])->exists();
    }

    /**
     * @param FindWeightParams $params
     * @return int
     */
    private function findWeight(FindWeightParams $params): int
    {
        $projectWeight = 0;
        if ($weight = ProjectWeight::find()->andWhere(['pw_project_id' => $params->projectId])->one()) {
            $projectWeight = (int)$weight->pw_weight;
        }

        $statusWeight = 0;
        if ($weight = StatusWeight::find()->andWhere(['sw_status_id' => $params->statusId])->one()) {
            $statusWeight = (int)$weight->sw_weight;
        }

        return ($projectWeight + $statusWeight);
    }
}
