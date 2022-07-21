<?php

namespace modules\taskList\src\services\taskCompletion;

use modules\taskList\src\entities\userTask\UserTask;

class TaskCompletionDictionary
{
    public static function getUserTaskProcessingStatuses(): array
    {
        return [UserTask::STATUS_PROCESSING];
    }
}
