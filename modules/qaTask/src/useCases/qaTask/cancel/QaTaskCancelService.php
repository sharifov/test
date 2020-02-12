<?php

namespace modules\qaTask\src\useCases\qaTask\cancel;

use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use sales\dispatchers\EventDispatcher;
use sales\repositories\user\UserRepository;

/**
 * Class QaTaskCancelService
 *
 * @property QaTaskRepository $taskRepository
 * @property UserRepository $userRepository
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskCancelService
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

    public function cancel(QaTaskCancelForm $form, int $userId): void
    {
        $task = $this->taskRepository->find($form->getTaskId());
        $user = $this->userRepository->find($userId);

        $oldAssignedUserId = $task->t_assigned_user_id;

        if (!$task->isProcessing()) {
            $task->processing();
        }

        if ($task->isAssigned($user->id)) {
            $task->assign($user->id);
        }

        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskCancelEvent(
            $task,
            $user->id,
            $oldAssignedUserId,
            $form->reasonId,
            $form->description
        ));
    }
}
