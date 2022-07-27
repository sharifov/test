<?php

namespace src\listeners\lead\leadBusinessExtraQueue;

use common\models\Lead;
use modules\featureFlag\FFlag;
use src\events\lead\LeadStatusChangedEvent;
use src\helpers\app\AppHelper;
use src\model\leadBusinessExtraQueue\service\LeadBusinessExtraQueueService;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogStatus;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;

class LeadBusinessExtraQueueRemoveOnStatusChangeListener
{
    public function handle(LeadStatusChangedEvent $event): void
    {
        try {
            $lead = $event->getLead();
            /** @fflag FFlag::FF_KEY_BEQ_ENABLE, Business Extra Queue enable */
            if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE) === false || !$lead->isBusinessType()) {
                return;
            }
            $description = null;
            if (($fromStatus = Lead::getStatus($event->oldStatus)) && $toStatus = Lead::getStatus($event->newStatus)) {
                $description = sprintf(LeadBusinessExtraQueueLogStatus::REASON_CHANGE_STATUS, $fromStatus, $toStatus);
            }
            LeadBusinessExtraQueueService::removeFromLead($lead, $description);
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(AppHelper::throwableLog($throwable), 'LeadBusinessExtraQueueRemoveOnStatusChangeListener:Exception');
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'LeadBusinessExtraQueueRemoveOnStatusChangeListener:Throwable');
        }
    }
}
