<?php

namespace modules\taskList\src\services;

use frontend\helpers\JsonHelper;
use modules\taskList\src\entities\taskList\TaskList;

class TaskListParamService
{
    private int $delayHours;
    private int $delayShift;

    public function __construct(TaskList $taskList)
    {
        $this->init(JsonHelper::decode($taskList->tl_params_json));
    }

    private function init(array $params)
    {
        $this->delayHours = (int)($params['delayHours'] ?? 0);
        $this->delayShift = (int)($params['delayShift'] ?? 0);
    }

    public function getDelayHoursParam(): int
    {
        return $this->delayHours;
    }

    public function getDelayShiftParam(): int
    {
        return $this->delayShift;
    }
}
