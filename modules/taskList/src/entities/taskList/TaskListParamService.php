<?php

namespace modules\taskList\src\entities\taskList;

use frontend\helpers\JsonHelper;
use modules\taskList\src\services\TaskListService;

class TaskListParamService
{
    private TaskList $taskList;
    private array $paramsJson;

    public function __construct(TaskList $taskList)
    {
        $this->taskList = $taskList;
        $this->paramsJson = (array) JsonHelper::decode($this->taskList->tl_params_json);
    }

    public function getExcludeProjectsAssigning(bool $asArray = true)
    {
        $excludeProjectsAssigning = $this->paramsJson[TaskListService::PARAM_KEY_SMS_EXCLUDE_PROJECTS] ?? null;
        return $asArray ? explode(',', $excludeProjectsAssigning) : $excludeProjectsAssigning;
    }
}
