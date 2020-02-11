<?php

namespace modules\qaTask\src\useCases\qaTask\action\take;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskActionTakeEvent
 *
 * @property QaTask $task
 * @property int $userId
 */
class QaTaskActionTakeEvent
{
    public $task;
    public $userId;

    public function __construct(QaTask $task, int $userId)
    {
        $this->task = $task;
        $this->userId = $userId;
    }
}
