<?php

namespace modules\qaTask\src\useCases\qaTask\create\lead\closeCheck;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskChangeStateInterface;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;

/**
 * Class QaTaskCreateLeadCloseCheckEvent
 * @package modules\qaTask\src\useCases\qaTask\create\lead\closeCheck
 *
 * @property QaTask $task
 * @property-read CreateDto $changeStateLog
 */
class QaTaskCreateLeadCloseCheckEvent implements QaTaskChangeStateInterface
{
    public QaTask $task;

    private CreateDto $changeStateLog;

    public function __construct(QaTask $task, CreateDto $changeStateLog)
    {
        $this->task = $task;
        $this->changeStateLog = $changeStateLog;
    }

    public function getChangeStateLog(): CreateDto
    {
        return $this->changeStateLog;
    }
}
