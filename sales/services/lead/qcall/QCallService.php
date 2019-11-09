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
     * @param int $status
     * @param int $callCount
     * @param int $weight
     * @param string|null $clientGmt
     */
    public function create(int $leadId, int $status, int $callCount, int $weight, ?string $clientGmt): void
    {
        if (!$config = $this->findConfig($status, $callCount)) {
            Yii::warning('QCallService:create. Config not found for status: ' . $status . ', callCount: ' . $callCount);
            return;
        }

        if (LeadQcall::find()->andWhere(['lqc_lead_id' => $leadId])->exists()) {
            Yii::error('QCallService:create. LeadId: ' . $leadId . ' is exists');
            return;
        }

        $interval = (new CalculateDateService())->calculate(
            $config->qc_time_from,
            $config->qc_time_to,
            $config->qc_client_time_enable,
            $clientGmt,
            'now'
        );

        $qCall = LeadQcall::create($leadId, $weight, $interval);

        $this->repository->save($qCall);
    }

    /**
     * @param int $leadId
     * @param int $status
     * @param int $callCount
     * @param string|null $clientGmt
     */
    public function updateInterval(int $leadId, int $status, int $callCount, ?string $clientGmt): void
    {
        if (!$config = $this->findConfig($status, $callCount)) {
            Yii::warning('QCallService:updateInterval. Config not found for status: ' . $status . ', callCount: ' . $callCount);
            return;
        }

        if (!$qCall = $this->findQcall($leadId)) {
            Yii::error('QCallService:updateInterval. Not found record LeadId: ' . $leadId);
            return;
        }

        $interval = (new CalculateDateService())->calculate(
            $config->qc_time_from,
            $config->qc_time_to,
            $config->qc_client_time_enable,
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
            Yii::error('QCallService:remove. Not found leadId: ' . $leadId);
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
     * @param int $status
     * @param int $callCount
     * @return QcallConfig|null
     */
    private function findConfig(int $status, int $callCount):? QcallConfig
    {
        return QcallConfig::find()->where(['qc_status_id' => $status])
            ->andWhere(['<=', 'qc_call_att', $callCount])
            ->orderBy(['qc_call_att' => SORT_DESC])->limit(1)->one();
    }
}
