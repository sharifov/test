<?php

namespace modules\qaTask\src\entities\qaTask\events;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskAssignEvent
 *
 * @property QaTask $task
 * @property int $userId
 */
class QaTaskAssignEvent
{
    public $task;
    public $userId;

    public function __construct(QaTask $task, int $userId)
    {
        $this->task = $task;
        $this->userId = $userId;
    }
}
