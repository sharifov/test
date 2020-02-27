<?php

namespace modules\qaTask\src\rbac\rules\task\actions\returnTask;

use modules\qaTask\src\entities\qaTask\QaTask;
use yii\rbac\Rule;

class QaTaskReturnEscalateRule extends Rule
{
    public $name = 'qa-task/qa-task-action/return_Escalate_Rule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['task']) || !$params['task'] instanceof QaTask) {
            return false;
        }
        /** @var QaTask $task */
        $task = $params['task'];
        return $task->isEscalated() && $task->isAssigned($userId);
    }
}
