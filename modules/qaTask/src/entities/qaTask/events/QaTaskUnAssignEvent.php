<?php

namespace modules\qaTask\src\entities\qaTask\events;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskUnAssignEvent
 *
 * @property QaTask $task
 * @property int $oldAssignedUserId
 */
class QaTaskUnAssignEvent
{
    public $task;
    public $oldAssignedUserId;

    public function __construct(QaTask $task, int $oldAssignedUserId)
    {
        $this->task = $task;
        $this->oldAssignedUserId = $oldAssignedUserId;
    }
}
