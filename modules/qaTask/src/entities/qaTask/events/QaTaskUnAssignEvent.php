<?php

namespace modules\qaTask\src\entities\qaTask\events;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskUnAssignEvent
 *
 * @property QaTask $task
 * @property int $userId
 */
class QaTaskUnAssignEvent
{
    public $task;
    public $userId;

    public function __construct(QaTask $task, int $userId)
    {
        $this->task = $task;
        $this->userId = $userId;
    }
}
