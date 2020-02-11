<?php

namespace modules\qaTask\src\useCases\qaTask\take;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskActionTakeEvent
 *
 * @property QaTask $task
 * @property int $userId
 */
class QaTaskTakeEvent
{
    public $task;
    public $userId;

    public function __construct(QaTask $task, int $userId)
    {
        $this->task = $task;
        $this->userId = $userId;
    }
}
