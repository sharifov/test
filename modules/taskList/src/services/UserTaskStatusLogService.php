<?php

namespace modules\taskList\src\services;

use modules\taskList\src\entities\userTask\repository\UserTaskStatusLogRepository;
use modules\taskList\src\entities\userTask\UserTaskHelper;
use modules\taskList\src\entities\userTask\UserTaskStatusLog;
use src\helpers\app\AppHelper;

class UserTaskStatusLogService
{
    public static function createLog(
        int $userTaskId,
        int $newStatusId,
        ?int $oldStatusId = null,
        ?string $description = null
    ): bool {
        if ($description === null) {
            if ($oldStatusId !== null) {
                $description = sprintf(
                    'User Task status changed from %s to %s',
                    UserTaskHelper::statusLabel($oldStatusId),
                    UserTaskHelper::statusLabel($newStatusId),
                );
            } else {
                $description = sprintf(
                    'User Task created with status %s',
                    UserTaskHelper::statusLabel($newStatusId)
                );
            }
        }

        $model = UserTaskStatusLog::create(
            $userTaskId,
            $newStatusId,
            $oldStatusId,
            $description
        );

        try {
            $repository = new UserTaskStatusLogRepository($model);
            $repository->save();

            return true;
        } catch (\RuntimeException $e) {
            \Yii::warning(
                AppHelper::throwableLog($e),
                'UserTaskStatusLogService:createLog'
            );
        }

        return false;
    }
}
