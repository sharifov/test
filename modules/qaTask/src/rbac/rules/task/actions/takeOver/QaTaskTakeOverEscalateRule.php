<?php

namespace modules\qaTask\src\rbac\rules\task\actions\takeOver;

use modules\qaTask\src\entities\qaTask\QaTask;
use yii\rbac\Rule;

class QaTaskTakeOverEscalateRule extends Rule
{
    public $name = 'qa-task/task/take-over_Escalate_Rule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['task']) || !$params['task'] instanceof QaTask) {
            return false;
        }
        /** @var QaTask $task */
        $task = $params['task'];
        return $task->isEscalated();
    }
}
