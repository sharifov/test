<?php

namespace modules\taskList\src\services\taskCompletion\taskCompletionChecker;

use common\models\Email;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\TaskObject;
use yii\db\ActiveRecordInterface;

class TaskListCompletionFactory
{
    private string $taskObject;
    private ActiveRecordInterface $taskModel;
    private TaskList $taskList;

    public function __construct(
        string $taskObject,
        ActiveRecordInterface $taskModel,
        TaskList $taskList
    ) {
        $this->taskObject = $taskObject;
        $this->taskModel = $taskModel;
        $this->taskList = $taskList;
    }

    public function create(): CompletionCheckerInterface
    {
        switch ($this->taskObject) {
            case TaskObject::OBJ_EMAIL:
                if (!$this->taskModel instanceof Email) {
                    throw new \RuntimeException('taskModel must be an instance of Email');
                }
                return (new EmailCompletionChecker($this->taskModel, $this->taskList));
        }
        throw new \RuntimeException('TaskObject (' . $this->taskObject . ') unprocessed');
    }
}
