<?php

namespace modules\qaTask\src\useCases\qaTask\create\manually;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskChangeStateInterface;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;

/**
 * Class QaTaskCreateManuallyEvent
 *
 * @property QaTask $task
 * @property CreateDto $changeStateLog
 */
class QaTaskCreateManuallyEvent implements QaTaskChangeStateInterface
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
