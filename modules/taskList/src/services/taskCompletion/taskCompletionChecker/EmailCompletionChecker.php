<?php

namespace modules\taskList\src\services\taskCompletion\taskCompletionChecker;

use common\models\Email;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\objects\email\EmailTaskDto;
use modules\taskList\src\objects\email\EmailTaskObject;
use src\access\ConditionExpressionService;
use src\access\CronExpressionService;

class EmailCompletionChecker implements CompletionCheckerInterface
{
    private Email $taskModel;
    private TaskList $taskList;

    public function __construct(
        Email $taskModel,
        TaskList $taskList
    ) {
        $this->taskModel = $taskModel;
        $this->taskList = $taskList;
    }

    public function check(): bool
    {
        if (!CronExpressionService::isDueCronExpression($this->taskList->tl_cron_expression)) {
            return false;
        }

        $emailTaskDto = new EmailTaskDto($this->taskModel);
        return ConditionExpressionService::isValidCondition(
            $this->taskList->tl_condition,
            [EmailTaskObject::OBJ_EMAIL => $emailTaskDto]
        );
    }
}
