<?php

namespace modules\qaTask\src\useCases\qaTask\action\takeOver;

use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use sales\dispatchers\EventDispatcher;
use sales\repositories\user\UserRepository;

/**
 * Class QaTaskActionTakeOverService
 *
 * @property QaTaskRepository $taskRepository
 * @property UserRepository $userRepository
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskActionTakeOverService
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

    public function takeOver(int $taskId, int $userId): void
    {
        $task = $this->taskRepository->find($taskId);
        $user = $this->userRepository->find($userId);

        if ($task->isAssigned($user->id)) {
            throw new \DomainException('Task is already assigned with this user.');
        }

        if (!($task->isProcessing() || $task->isEscalated())) {
            throw new \DomainException('Current status is denied.');
        }

        if (!$task->isUnAssigned()) {
            throw new \DomainException('Task is already assigned.');
        }

        $oldAssignedUserId = $task->t_assigned_user_id;

        if (!$task->isProcessing()) {
            $task->processing();
        }
        $task->assign($user->id);
        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskActionTakeOverEvent($task, $user->id, $oldAssignedUserId));
    }
}
