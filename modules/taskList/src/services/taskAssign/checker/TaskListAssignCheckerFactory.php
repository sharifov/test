<?php

namespace modules\taskList\src\services\taskAssign\checker;

use common\models\Lead;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\TaskObject;

class TaskListAssignCheckerFactory
{
    private TaskList $taskList;
    private string $taskObject;
    private Lead $lead;

    public function __construct(
        TaskList $taskList,
        Lead $lead
    ) {
        $this->taskList = $taskList;
        $this->taskObject = $taskList->tl_object;
        $this->lead = $lead;
    }

    public function create(): TaskAssignCheckerInterface
    {
        switch ($this->taskObject) {
            case TaskObject::OBJ_EMAIL:
                return (new EmailAssignChecker($this->lead));

            case TaskObject::OBJ_SMS:
                return (new SmsAssignChecker($this->lead, $this->taskList));
            case TaskObject::OBJ_CALL:
                return (new PhoneAssignChecker($this->lead));
        }
        throw new \RuntimeException('TaskObject (' . $this->taskObject . ') unprocessed');
    }
}
