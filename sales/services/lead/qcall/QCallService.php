<?php

namespace sales\services\lead\qcall;

use sales\repositories\lead\LeadQcallRepository;
use Yii;
use common\models\LeadQcall;
use common\models\QcallConfig;

/**
 * Class QCallService
 *
 * @property LeadQcallRepository $repository
 */
class QCallService
{
    private $repository;

    /**
     * @param LeadQcallRepository $repository
     */
    public function __construct(LeadQcallRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $leadId
     * @param Config $config
     * @param int $weight
     * @param string|null $clientGmt
     */
    public function create(int $leadId, Config $config, int $weight, ?string $clientGmt): void
    {
        if (!$qConfig = $this->findConfig($config)) {
            Yii::warning('QCallService:create. Config not found for status: ' . $config->status . ', callCount: ' . $config->callCount);
            return;
        }

        if (LeadQcall::find()->andWhere(['lqc_lead_id' => $leadId])->exists()) {
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

        $qCall = LeadQcall::create($leadId, $weight, $interval);

        $this->repository->save($qCall);
    }

    /**
     * @param LeadQcall $qCall
     * @param Config $config
     * @param string|null $clientGmt
     */
    public function updateInterval(LeadQcall $qCall, Config $config, ?string $clientGmt): void
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
}
