<?php

namespace src\listeners\lead;

use common\components\jobs\LeadTaskListJob;
use modules\lead\src\services\LeadTaskListService;
use src\events\lead\LeadOwnerChangedEvent;
use src\helpers\app\AppHelper;

/**
 * Class LeadTaskListListener
 */
class LeadTaskListListener
{
    public function handle(LeadOwnerChangedEvent $event): void
    {
        try {
            if (!(new LeadTaskListService($event->getLead()))->isProcessAllowed(false)) {
                return;
            }

            $job = new LeadTaskListJob($event->lead->id, ((int) $event->newOwnerId !== (int) $event->oldOwnerId));
            \Yii::$app->queue_job->priority(100)->push($job);
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(AppHelper::throwableLog($throwable), 'LeadTaskListListener:handle:Exception');
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'LeadTaskListListener:handle:Throwable');
        }
    }
}
