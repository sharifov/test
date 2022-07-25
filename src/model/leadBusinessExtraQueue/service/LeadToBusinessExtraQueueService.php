<?php

namespace src\model\leadBusinessExtraQueue\service;

use common\components\purifier\Purifier;
use common\models\Lead;
use common\models\Notifications;
use src\helpers\ErrorsToStringHelper;
use src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueue;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLog;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogStatus;
use src\model\leadBusinessExtraQueueLog\repository\LeadBusinessExtraQueueLogRepository;
use src\repositories\lead\LeadRepository;
use yii\db\Transaction;

class LeadToBusinessExtraQueueService
{
    private int $leadId;
    private string $ruleId;
    private LeadRepository $leadRepository;

    public function __construct(int $leadId, int $ruleId, LeadRepository $leadRepository)
    {
        $this->leadId = $leadId;
        $this->ruleId = $ruleId;
        $this->leadRepository = $leadRepository;
    }

    public function handle(): void
    {
        $transaction = new Transaction(['db' => \Yii::$app->db]);

        try {
            $transaction->begin();

            if (!$lead = Lead::find()->where(['id' => $this->leadId])->limit(1)->one()) {
                throw new \RuntimeException('Lead not found by ID(' . $this->leadId . ')');
            }

            $ownerId = $lead->employee_id;
            $lead->toBusinessExtraQueue($ownerId);
            $this->leadRepository->save($lead);
            LeadBusinessExtraQueue::deleteAll(['lbeq_lead_id' => $lead->id]);
            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            throw $throwable;
        }
        $reason = LeadBusinessExtraQueueLogStatus::REASON_ADDED_TO_BUSINESS_EXTRA_QUEUE_DUE_EXPIRATION_TIME;
        $leadBusinessExtraQueueLog = LeadBusinessExtraQueueLog::create(
            $lead->id,
            $this->ruleId,
            $ownerId,
            LeadBusinessExtraQueueLogStatus::STATUS_ADDED_TO_BUSINESS_EXTRA_QUEUE,
            $reason
        );
        if (!$leadBusinessExtraQueueLog->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($leadBusinessExtraQueueLog, ' '));
        }

        $leadBusinessExtraQueueLogRepository = new LeadBusinessExtraQueueLogRepository($leadBusinessExtraQueueLog);
        $leadBusinessExtraQueueLogRepository->save();

        if ($lead->hasOwner()) {
            $message = 'Lead(' . Purifier::createLeadShortLink($lead) . ') transfer to Business Extra Queue. ' . $reason;

            Notifications::createAndPublish(
                $ownerId,
                'Lead transfer to Business Extra Queue',
                $message,
                Notifications::TYPE_INFO,
                true
            );
        }
    }
}
