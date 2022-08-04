<?php

namespace modules\taskList\src\services\taskAssign\checker;

use common\models\Lead;
use modules\taskList\src\entities\TaskObject;

class TaskListAssignCheckerFactory
{
    private string $taskObject;
    private Lead $lead;

    public function __construct(
        string $taskObject,
        Lead $lead
    ) {
        $this->taskObject = $taskObject;
        $this->lead = $lead;
    }

    public function create(): TaskAssignCheckerInterface
    {
        switch ($this->taskObject) {
            case TaskObject::OBJ_EMAIL:
                return (new EmailAssignChecker($this->lead));

            case TaskObject::OBJ_SMS:
            case TaskObject::OBJ_CALL:
                return (new PhoneAssignChecker($this->lead));
        }
        throw new \RuntimeException('TaskObject (' . $this->taskObject . ') unprocessed');
    }
}
