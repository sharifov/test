<?php

namespace modules\taskList\src\services\taskCompletion\taskCompletionChecker;

use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\objects\email\EmailTaskDto;
use modules\taskList\src\objects\email\EmailTaskObject;
use src\access\ConditionExpressionService;
use src\access\CronExpressionService;
use src\entities\email\EmailInterface;

class EmailCompletionChecker implements CompletionCheckerInterface
{
    private EmailInterface $taskModel;
    private TaskList $taskList;

    public function __construct(
        EmailInterface $taskModel,
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
