<?php

namespace modules\taskList\src\listeners;

use modules\taskList\src\events\UserTaskStatusChangedEvent;
use modules\taskList\src\services\UserTaskStatusLogService;
use src\helpers\app\AppHelper;
use Yii;
use yii\helpers\ArrayHelper;

class UserTaskStatusChangedListener
{
    public function handle(UserTaskStatusChangedEvent $event): void
    {
        try {
            if ($event->oldStatusId === null) {
                UserTaskStatusLogService::createLog(
                    $event->userTask->ut_id,
                    $event->newStatusId
                );
            } else {
                UserTaskStatusLogService::createLog(
                    $event->userTask->ut_id,
                    $event->newStatusId,
                    $event->oldStatusId
                );
            }
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), ['UserTaskID' => $event->userTask->ut_id]);
            Yii::warning($message, 'UserTaskStatusChangedListener::handle::Throwable');
        }
    }
}
