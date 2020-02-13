<?php

namespace modules\qaTask\src\useCases\qaTask\takeOver;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskChangeStateInterface;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;

/**
 * Class QaTaskActionTakeOverEvent
 *
 * @property QaTask $task
 * @property int|null $oldAssignedUserId
 * @property CreateDto $changeStateLog
 */
class QaTaskTakeOverEvent implements QaTaskChangeStateInterface
{
    public $task;
    public $oldAssignedUserId;

    private $changeStateLog;

    public function __construct(QaTask $task, ?int $oldAssignedUserId, CreateDto $changeStateLog)
    {
        $this->task = $task;
        $this->oldAssignedUserId = $oldAssignedUserId;
        $this->changeStateLog = $changeStateLog;
    }

    public function getChangeStateLog(): CreateDto
    {
        return clone $this->changeStateLog;
    }
}
