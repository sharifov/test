<?php

namespace modules\qaTask\src\entities\qaTask\events;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskAssignEvent
 *
 * @property QaTask $task
 * @property \DateTimeImmutable $date
 */
class QaTaskDeadlineEvent
{
    public $task;
    public $date;

    public function __construct(QaTask $task, \DateTimeImmutable $date)
    {
        $this->task = $task;
        $this->date = $date;
    }
}
