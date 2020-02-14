<?php

namespace modules\qaTask\src\rbac\rules\task\actions\cancel;

use modules\qaTask\src\entities\qaTask\QaTask;
use yii\rbac\Rule;

class QaTaskCancelProcessingCurrentRule extends Rule
{
    public $name = 'qa-task/task/cancel_Processing_Current_Rule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['task']) || !$params['task'] instanceof QaTask) {
            return false;
        }
        /** @var QaTask $task */
        $task = $params['task'];
        return $task->isProcessing() && $task->isAssigned($userId);
    }
}
