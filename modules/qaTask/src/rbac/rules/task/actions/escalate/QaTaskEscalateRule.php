<?php

namespace modules\qaTask\src\rbac\rules\task\actions\escalate;

use modules\qaTask\src\entities\qaTask\QaTask;
use yii\rbac\Rule;

class QaTaskEscalateRule extends Rule
{
    public $name = 'qa-task/task/escalate_Rule';

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
