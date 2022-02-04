<?php

namespace src\listeners\lead;

use common\models\Employee;
use common\models\Lead;
use src\events\lead\LeadOwnerChangedEvent;
use src\events\lead\LeadStatusChangedEvent;
use src\helpers\app\AppHelper;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;

/**
 * Class LeadPoorProcessingRemoverStatusChangedListener
 */
class LeadPoorProcessingRemoverStatusChangedListener
{
    public function handle(LeadStatusChangedEvent $event): void
    {
        try {
            $description = null;
            if (($fromStatus = Lead::getStatus($event->oldStatus)) && $toStatus = Lead::getStatus($event->newStatus)) {
                $description = sprintf(LeadPoorProcessingLogStatus::REASON_CHANGE_STATUS, $fromStatus, $toStatus);
            }

            LeadPoorProcessingService::removeFromLead($event->getLead(), $description);
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(AppHelper::throwableLog($throwable), 'LeadPoorProcessingRemoverStatusChangedListener:Exception');
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'LeadPoorProcessingRemoverStatusChangedListener:Throwable');
        }
    }
}
