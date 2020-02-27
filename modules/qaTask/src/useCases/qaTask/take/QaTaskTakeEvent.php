<?php

namespace modules\qaTask\src\useCases\qaTask\take;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskChangeStateInterface;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;

/**
 * Class QaTaskActionTakeEvent
 *
 * @property QaTask $task
 * @property CreateDto $changeStateLog
 */
class QaTaskTakeEvent implements QaTaskChangeStateInterface
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
