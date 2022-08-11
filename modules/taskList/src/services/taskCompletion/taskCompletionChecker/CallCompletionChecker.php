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

        /** @fflag FFlag::FF_KEY_USER_TASK_COMPLETION_DEBUG, Enable debug/log mode */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_USER_TASK_COMPLETION_DEBUG)) {
            $logData['target_object_call_attempts'] = $callTaskDto->target_object_call_attempts;
            $logData['target_object_call_completed'] = $callTaskDto->target_object_call_completed;
            $logData['c_status_id'] = $this->taskModel->c_status_id;
            $logData['c_call_status'] = $this->taskModel->c_call_status;

            \Yii::info($logData, 'info\UserTaskCompletionService:point:' . 7);
        }

        return ConditionExpressionService::isValidCondition(
            $this->taskList->tl_condition,
            [CallTaskObject::OBJ_CALL => $callTaskDto]
        );
    }
}
