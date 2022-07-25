<?php

namespace modules\taskList\src\services\taskCompletion\taskCompletionChecker;

use common\models\Call;
use common\models\Email;
use common\models\Sms;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\TaskObject;
use yii\db\ActiveRecordInterface;
use src\entities\email\EmailInterface;

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
                if (!$this->taskModel instanceof EmailInterface) {
                    throw new \RuntimeException('taskModel must be an instance of Email');
                }
                return (new EmailCompletionChecker($this->taskModel, $this->taskList));

            case TaskObject::OBJ_SMS:
                if (!$this->taskModel instanceof Sms) {
                    throw new \RuntimeException('taskModel must be an instance of Sms');
                }
                return (new SmsCompletionChecker($this->taskModel, $this->taskList));

            case TaskObject::OBJ_CALL:
                if (!$this->taskModel instanceof Call) {
                    throw new \RuntimeException('taskModel must be an instance of Call');
                }
                return (new CallCompletionChecker($this->taskModel, $this->taskList));
        }
        throw new \RuntimeException('TaskObject (' . $this->taskObject . ') unprocessed');
    }
}
