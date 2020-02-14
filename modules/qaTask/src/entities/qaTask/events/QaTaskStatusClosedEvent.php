<?php

namespace modules\qaTask\src\entities\qaTask\events;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskStatusClosedEvent
 *
 * @property QaTask $task
 */
class QaTaskStatusClosedEvent
{
    public $task;

    public function __construct(QaTask $task)
    {
        $this->task = $task;
    }
}
