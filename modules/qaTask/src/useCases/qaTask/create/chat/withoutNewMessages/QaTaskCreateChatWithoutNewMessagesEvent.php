<?php

namespace modules\qaTask\src\useCases\qaTask\create\chat\withoutNewMessages;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskChangeStateInterface;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;

/**
 * Class QaTaskCreateChatWithoutNewMessagesEvent
 * @package modules\qaTask\src\useCases\qaTask\create\chat\withoutNewMessages
 *
 * @property QaTask $task
 * @property-read CreateDto $changeStateLog
 */
class QaTaskCreateChatWithoutNewMessagesEvent implements QaTaskChangeStateInterface
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
