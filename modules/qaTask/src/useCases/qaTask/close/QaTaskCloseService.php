<?php

namespace modules\qaTask\src\useCases\qaTask\close;

use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use sales\access\EmployeeProjectAccess;
use sales\dispatchers\EventDispatcher;
use sales\repositories\user\UserRepository;

/**
 * Class QaTaskCloseService
 *
 * @property QaTaskRepository $taskRepository
 * @property UserRepository $userRepository
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskCloseService
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

    public function close(QaTaskCloseForm $form, int $userId): void
    {
        $task = $this->taskRepository->find($form->getTaskId());
        $user = $this->userRepository->find($userId);

        EmployeeProjectAccess::guard($task->t_project_id, $user->id);

        $task->closed();

        if ($form->rating) {
            $task->changeRating($form->rating);
        }

        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskCloseEvent(
            $task,
            $form->description
        ));
    }
}
