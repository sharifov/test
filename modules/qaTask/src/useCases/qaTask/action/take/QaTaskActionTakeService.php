<?php

namespace modules\qaTask\src\useCases\qaTask\action\take;

use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use sales\dispatchers\EventDispatcher;
use sales\repositories\user\UserRepository;

/**
 * Class QaTaskActionTakeService
 *
 * @property QaTaskRepository $taskRepository
 * @property UserRepository $userRepository
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskActionTakeService
{
    private $taskRepository;
    private $userRepository;
    private $eventDispatcher;

    public function __construct(
        QaTaskRepository $taskRepository,
        UserRepository $userRepository,
        EventDispatcher $eventDispatcher
    )
    {
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function take(int $taskId, int $userId): void
    {
        $task = $this->taskRepository->find($taskId);
        $user = $this->userRepository->find($userId);

        if (!($task->isPending() || $task->isEscalated())) {
            throw new \DomainException('Current status is denied.');
        }

        if (!$task->isUnAssigned()) {
            throw new \DomainException('Task is already assigned.');
        }

        $task->processing();
        $task->assign($user->id);
        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskActionTakeEvent($task, $user->id));
    }
}
