<?php

namespace src\listeners\lead;

use common\components\jobs\LeadPoorProcessingJob;
use src\events\lead\LeadPoorProcessingEvent;
use src\helpers\app\AppHelper;
use Yii;

/**
 * Class LeadPoorProcessingAdderListener
 */
class LeadPoorProcessingAdderListener
{
    public function handle(LeadPoorProcessingEvent $event): void
    {
        try {
            $job = new LeadPoorProcessingJob($event->getLead()->id, $event->getDataKey());
            Yii::$app->queue_job->priority(100)->push($job);
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(AppHelper::throwableLog($throwable), 'LeadPoorProcessingAdderListener:Exception');
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'LeadPoorProcessingAdderListener:Throwable');
        }
    }
}
