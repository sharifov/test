<?php

namespace modules\qaTask\src\entities\qaTask\events;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskEscalatedEvent
 *
 * @property QaTask $task
 */
class QaTaskEscalatedEvent
{
    public $task;

    public function __construct(QaTask $task)
    {
        $this->task = $task;
    }
}
