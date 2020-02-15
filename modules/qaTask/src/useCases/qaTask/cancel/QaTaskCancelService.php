<?php

namespace modules\qaTask\src\useCases\qaTask\cancel;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use sales\access\EmployeeProjectAccess;
use sales\dispatchers\EventDispatcher;
use sales\repositories\user\UserRepository;
use yii\web\ForbiddenHttpException;

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

    public function cancel(QaTaskCancelForm $form): void
    {
        $task = $this->taskRepository->find($form->getTaskId());
        $user = $this->userRepository->find($form->getUserId());

        self::businessGuard($task, $user->id);

        $startStatusId = $task->t_status_id;

        $task->canceled();
        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskCancelEvent(
            $task,
            new CreateDto(
                $task->t_id,
                $startStatusId,
                $task->t_status_id,
                $form->reasonId,
                $form->description,
                QaTaskActions::CANCEL,
                $task->t_assigned_user_id,
                $user->id
            )
        ));
    }

    private static function businessGuard(QaTask $task, int $userId): void
    {
        EmployeeProjectAccess::guard($task->t_project_id, $userId);

        if ($task->isCanceled()) {
            throw new \DomainException('Task is already is canceled.');
        }
    }

    /**
     * @param QaTask $task
     * @throws ForbiddenHttpException
     */
    public static function permissionGuard(QaTask $task): void
    {
        if (!\Yii::$app->user->can('qa-task/qa-task-action/cancel', ['task' => $task])) {
            throw new ForbiddenHttpException('Access denied.');
        }
    }

    public static function can(QaTask $task, int $userId): bool
    {
        try {
            self::permissionGuard($task);
            self::businessGuard($task, $userId);
        } catch (\Throwable $e) {
            return false;
        }
        return true;
    }
}
