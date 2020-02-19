<?php

namespace modules\qaTask\src\rbac\rules\task\actions\cancel;

use modules\qaTask\src\entities\qaTask\QaTask;
use yii\rbac\Rule;

class QaTaskCancelProcessingRule extends Rule
{
    public $name = 'qa-task/qa-task-action/cancel_Processing_Rule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['task']) || !$params['task'] instanceof QaTask) {
            return false;
        }
        /** @var QaTask $task */
        $task = $params['task'];
        return $task->isProcessing();
    }
}
