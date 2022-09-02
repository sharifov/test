<?php

namespace modules\objectTask\src\services;

use modules\lead\src\abac\LeadAbacObject;
use modules\objectTask\src\abac\ObjectTaskObject;
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

    public static function linkIsVisibleInSidebar(): bool
    {
        /** @fflag FFlag::FF_KEY_OBJECT_TASK_STATUS_LOG_ENABLE, Object Task status log enable */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_OBJECT_TASK_STATUS_LOG_ENABLE) === true) {
            /** @abac ObjectTaskObject::ACT_OBJECT_TASK_STATUS_LOG, ObjectTaskObject::ACTION_ACCESS, Access to page /object-task/object-task-status-log/index */
            return \Yii::$app->abac->can(
                null,
                ObjectTaskObject::ACT_OBJECT_TASK_STATUS_LOG,
                ObjectTaskObject::ACTION_ACCESS
            );
        }

        return false;
    }
}
