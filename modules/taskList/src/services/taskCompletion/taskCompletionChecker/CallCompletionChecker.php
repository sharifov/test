<?php

namespace modules\taskList\src\services\taskCompletion\taskCompletionChecker;

use common\models\Call;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\objects\call\CallTaskDTO;
use modules\taskList\src\objects\call\CallTaskObject;
use src\access\ConditionExpressionService;
use src\access\CronExpressionService;

class CallCompletionChecker implements CompletionCheckerInterface
{
    private Call $taskModel;
    private TaskList $taskList;
    private ?string $userTaskStartDT;
    private ?string $userTaskEndDT;

    public function __construct(
        Call $taskModel,
        TaskList $taskList,
        ?string $userTaskStartDT = null,
        ?string $userTaskEndDT = null
    ) {
        $this->taskModel = $taskModel;
        $this->taskList = $taskList;
        $this->userTaskStartDT = $userTaskStartDT;
        $this->userTaskEndDT = $userTaskEndDT;
    }

    public function check(): bool
    {
        if (!CronExpressionService::isDueCronExpression($this->taskList->tl_cron_expression)) {
            return false;
        }

        $callTaskDto = new CallTaskDTO($this->taskModel, $this->userTaskStartDT, $this->userTaskEndDT);
        return ConditionExpressionService::isValidCondition(
            $this->taskList->tl_condition,
            [CallTaskObject::OBJ_CALL => $callTaskDto]
        );
    }
}
