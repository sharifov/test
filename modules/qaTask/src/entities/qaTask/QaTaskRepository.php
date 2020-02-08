<?php

namespace modules\qaTask\src\entities\qaTask;

use modules\qaTask\src\exceptions\QaTaskCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class QaTaskRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): QaTask
    {
        if ($task = QaTask::findOne($id)) {
            return $task;
        }
        throw new NotFoundException('Qa Task is not found', QaTaskCodeException::QA_TASK_NOT_FOUND);
    }

    public function save(QaTask $task): int
    {
        if (!$task->save(false)) {
            throw new \RuntimeException('Saving error', QaTaskCodeException::QA_TASK_SAVE);
        }
        $this->eventDispatcher->dispatchAll($task->releaseEvents());
        return $task->t_id;
    }

    public function remove(QaTask $task): void
    {
        if (!$task->delete()) {
            throw new \RuntimeException('Removing error', QaTaskCodeException::QA_TASK_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($task->releaseEvents());
    }
}
