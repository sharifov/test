<?php

namespace modules\qaTask\src\entities\qaTask\events;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskCanceledEvent
 *
 * @property QaTask $task
 */
class QaTaskCanceledEvent
{
    public $task;

    public function __construct(QaTask $task)
    {
        $this->task = $task;
    }
}
