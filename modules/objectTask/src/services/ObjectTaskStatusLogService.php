<?php

namespace modules\objectTask\src\services;

use modules\objectTask\src\entities\ObjectTaskStatusLog;
use modules\objectTask\src\entities\repositories\ObjectTaskStatusLogRepository;
use src\helpers\app\AppHelper;

class ObjectTaskStatusLogService
{
    public static function createLog(string $objectTaskUuid, int $newStatus, ?int $oldStatus = null, ?string $description = null): bool
    {
        $model = ObjectTaskStatusLog::create(
            $objectTaskUuid,
            $newStatus,
            $oldStatus,
            $description
        );

        try {
            (new ObjectTaskStatusLogRepository($model))->save();

            return true;
        } catch (\Throwable $e) {
            \Yii::warning(
                AppHelper::throwableLog($e),
                'ObjectTaskStatusLogService:createLog'
            );
        }

        return false;
    }
}
