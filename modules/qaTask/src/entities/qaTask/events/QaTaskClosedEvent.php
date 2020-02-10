<?php

namespace modules\qaTask\src\entities\qaTask\events;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskClosedEvent
 *
 * @property QaTask $task
 */
class QaTaskClosedEvent
{
    public $task;

    public function __construct(QaTask $task)
    {
        $this->task = $task;
    }
}
