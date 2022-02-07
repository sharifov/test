<?php

namespace src\listeners\lead;

use src\events\lead\LeadableEventInterface;
use src\helpers\app\AppHelper;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use Yii;

/**
 * Class LeadPoorProcessingRemoverListener
 */
class LeadPoorProcessingRemoverListener
{
    public function handle(LeadableEventInterface $event): void
    {
        try {
            $lead = $event->getLead();
            LeadPoorProcessingService::removeFromLead($lead);
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(AppHelper::throwableLog($throwable), 'Listeners:LeadPoorProcessingRemoverListener:Exception');
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'Listeners:LeadPoorProcessingRemoverListener:Throwable');
        }
    }
}
