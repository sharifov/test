<?php

namespace modules\qaTask\src\useCases\qaTask\escalate;

use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use sales\dispatchers\EventDispatcher;
use sales\repositories\user\UserRepository;

/**
 * Class QaTaskCloseService
 *
 * @property QaTaskRepository $taskRepository
 * @property UserRepository $userRepository
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskEscalateService
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

    public function escalate(QaTaskEscalateForm $form): void
    {
        $task = $this->taskRepository->find($form->getTaskId());
        $user = $this->userRepository->find($form->getUserId());

        if (!$task->isProcessing()) {
            throw new \DomainException('Task must be in processing.');
        }

        if (!$task->isAssigned($user->id)) {
            throw new \DomainException('User must be assigned.');
        }

        $task->escalated();
        $task->changeRating($form->rating);
        
        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskEscalateEvent(
            $task,
            $form->reasonId,
            $form->description
        ));
    }
}
