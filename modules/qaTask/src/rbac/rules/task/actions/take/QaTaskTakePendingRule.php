<?php

namespace modules\qaTask\src\rbac\rules\task\actions\take;

use modules\qaTask\src\entities\qaTask\QaTask;
use yii\rbac\Rule;

class QaTaskTakePendingRule extends Rule
{
    public $name = 'qa-task/qa-task-action/take_Pending_Rule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['task']) || !$params['task'] instanceof QaTask) {
            return false;
        }
        /** @var QaTask $task */
        $task = $params['task'];
        return $task->isPending();
    }
}
