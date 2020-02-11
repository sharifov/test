<?php

namespace modules\qaTask\src\useCases\qaTask\takeOver;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskActionTakeOverEvent
 *
 * @property QaTask $task
 * @property int $newAssignUserId
 * @property int|null $oldAssignedUserId
 */
class QaTaskTakeOverEvent
{
    public $task;
    public $newAssignUserId;
    public $oldAssignedUserId;

    public function __construct(QaTask $task, int $newAssignUserId, ?int $oldAssignedUserId)
    {
        $this->task = $task;
        $this->newAssignUserId = $newAssignUserId;
        $this->oldAssignedUserId = $oldAssignedUserId;
    }
}
