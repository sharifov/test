<?php

namespace modules\taskList\src\services\taskCompletion\taskCompletionChecker;

use common\models\Sms;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\objects\sms\SmsTaskDto;
use modules\taskList\src\objects\sms\SmsTaskObject;
use src\access\ConditionExpressionService;
use src\access\CronExpressionService;

class SmsCompletionChecker implements CompletionCheckerInterface
{
    private Sms $taskModel;
    private TaskList $taskList;

    public function __construct(
        Sms $taskModel,
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

        $smsTaskDto = new SmsTaskDto($this->taskModel);

        return ConditionExpressionService::isValidCondition(
            $this->taskList->tl_condition,
            [SmsTaskObject::OBJ_SMS => $smsTaskDto]
        );
    }
}
