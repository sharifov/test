<?php

namespace modules\qaTask\src\entities\qaTask\events;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskStatusCanceledEvent
 *
 * @property QaTask $task
 */
class QaTaskStatusCanceledEvent
{
    public $task;

    public function __construct(QaTask $task)
    {
        $this->task = $task;
    }
}
