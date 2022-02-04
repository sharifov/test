<?php

namespace src\listeners\lead;

use src\events\lead\LeadPoorProcessingEvent;
use src\helpers\app\AppHelper;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;

/**
 * Class LeadPoorProcessingAdderListener
 */
class LeadPoorProcessingAdderListener
{
    public function handle(LeadPoorProcessingEvent $event): void
    {
        try {
            if (!LeadPoorProcessingDataQuery::isExistActiveRule($event->getDataKey())) {
                throw new \RuntimeException('Rule (' . $event->getDataKey() . ') not enabled');
            }

            LeadPoorProcessingService::addLeadPoorProcessingJob(
                $event->getLead()->id,
                $event->getDataKey(),
                $event->getDescription()
            );
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::info(AppHelper::throwableLog($throwable), 'info\LeadPoorProcessingAdderListener:Exception');
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'LeadPoorProcessingAdderListener:Throwable');
        }
    }
}
