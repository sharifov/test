<?php

namespace sales\services\lead\qcall;

use common\models\Lead;
use sales\repositories\lead\LeadFlowRepository;
use sales\repositories\lead\LeadQcallRepository;
use Yii;
use common\models\LeadQcall;
use common\models\QcallConfig;

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

    public function createOrUpdate(Lead $lead): void
    {
        if ($lq = $lead->leadQcall) {
            $this->updateInterval($lq, new Config($lead->status, $lead->getCountOutCallsLastFlow()), $lead->offset_gmt);

        } else {
            $this->create($lead->id, new Config($lead->status, $lead->getCountOutCallsLastFlow()), ($lead->project_id * 10), $lead->offset_gmt);
        }
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

    public function resetAttempts(Lead $lead): void
    {
        if ($lastLeadFlow = $lead->lastLeadFlow) {
            $lastLeadFlow->resetAttempts();
            $this->leadFlowRepository->save($lastLeadFlow);
        }
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
