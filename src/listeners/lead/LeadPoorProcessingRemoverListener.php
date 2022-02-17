<?php

namespace src\listeners\lead;

use common\models\Lead;
use src\events\lead\LeadStatusChangedEvent;
use src\helpers\app\AppHelper;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use Yii;

/**
 * Class LeadPoorProcessingRemoverListener
 */
class LeadPoorProcessingRemoverListener
{
    public function handle(LeadStatusChangedEvent $event): void
    {
        try {
            $lead = $event->getLead();
            $description = null;
            if (($fromStatus = Lead::getStatus($event->oldStatus)) && $toStatus = Lead::getStatus($event->newStatus)) {
                $description = sprintf(LeadPoorProcessingLogStatus::REASON_CHANGE_STATUS, $fromStatus, $toStatus);
            }
            LeadPoorProcessingService::removeFromLead($lead, $description);
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(AppHelper::throwableLog($throwable), 'Listeners:LeadPoorProcessingRemoverListener:Exception');
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'Listeners:LeadPoorProcessingRemoverListener:Throwable');
        }
    }
}
