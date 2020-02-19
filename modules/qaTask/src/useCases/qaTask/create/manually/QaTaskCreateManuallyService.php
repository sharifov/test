<?php

namespace modules\qaTask\src\useCases\qaTask\create\manually;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskCreateType;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use sales\dispatchers\EventDispatcher;

/**
 * Class QaTaskCreateManuallyService
 *
 * @property QaTaskRepository $taskRepository
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskCreateManuallyService
{
    private $taskRepository;
    private $eventDispatcher;

    public function __construct(
        QaTaskRepository $taskRepository,
        EventDispatcher $eventDispatcher
    )
    {
        $this->taskRepository = $taskRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(QaTaskCreateManuallyForm $form): void
    {
        $task = QaTask::create(
            $form->objectType,
            $form->objectId,
            $form->projectId,
            $form->departmentId,
            $form->categoryId,
            QaTaskCreateType::MANUALLY,
            $form->description
        );

        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskCreateManuallyEvent(
            $task,
            new CreateDto(
                $task,
                null,
                $task->t_status_id,
                null,
                null,
                QaTaskActions::CREATE,
                $task->t_assigned_user_id,
                $form->createdUserId
            )
        ));
    }
}
