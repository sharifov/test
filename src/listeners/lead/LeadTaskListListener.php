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
            if ($event->getNewOwnerId() === $event->getOldOwnerId()) {
                \modules\taskList\src\helpers\TaskListHelper::debug(
                    'NewOwner(' . $event->getNewOwnerId() . ') === OldOwner (' . $event->getOldOwnerId() . ')',
                    'info\UserTaskAssign:LeadTaskListListener:handle:info'
                );
                return;
            }
            if (!(new LeadTaskListService($event->getLead()))->isEnableFFAndNotEmptyOwner(false)) {
                return;
            }

            $job = new LeadTaskListJob($event->lead->id, $event->getOldOwnerId());
            \Yii::$app->queue_job->priority(100)->delay(5)->push($job);
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(AppHelper::throwableLog($throwable), 'LeadTaskListListener:handle:Exception');
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'LeadTaskListListener:handle:Throwable');
        }
    }
}
