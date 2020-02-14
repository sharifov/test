<?php

namespace modules\qaTask\src\useCases\qaTask\cancel;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskChangeStateInterface;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;

/**
 * Class QaTaskCancelEvent
 *
 * @property QaTask $task
 * @property CreateDto $changeStateLog
 */
class QaTaskCancelEvent implements QaTaskChangeStateInterface
{
    public $task;

    private $changeStateLog;

    public function __construct(QaTask $task, CreateDto $changeStateLog)
    {
        $this->task = $task;
        $this->changeStateLog = $changeStateLog;
    }

    public function getChangeStateLog(): CreateDto
    {
        return clone $this->changeStateLog;
    }
}
