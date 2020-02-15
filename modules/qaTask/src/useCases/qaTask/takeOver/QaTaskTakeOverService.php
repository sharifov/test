<?php

namespace modules\qaTask\src\useCases\qaTask\takeOver;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use sales\access\EmployeeProjectAccess;
use sales\dispatchers\EventDispatcher;
use sales\repositories\user\UserRepository;
use yii\web\ForbiddenHttpException;

/**
 * Class QaTaskActionTakeOverService
 *
 * @property QaTaskRepository $taskRepository
 * @property UserRepository $userRepository
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskTakeOverService
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

    public function takeOver(QaTaskTakeOverForm $form): void
    {
        $task = $this->taskRepository->find($form->getTaskId());
        $user = $this->userRepository->find($form->getUserId());

        self::businessGuard($task, $user->id);

        $oldAssignedUserId = $task->t_assigned_user_id;
        $startStatusId = $task->t_status_id;

        if (!$task->isProcessing()) {
            $task->processing();
        }

        $task->assign($user->id);
        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskTakeOverEvent(
            $task,
            $oldAssignedUserId,
            new CreateDto(
                $task->t_id,
                $startStatusId,
                $task->t_status_id,
                $form->reasonId,
                $form->description,
                QaTaskActions::TAKE_OVER,
                $task->t_assigned_user_id,
                $user->id
            )
        ));
    }

    private static function businessGuard(QaTask $task, int $userId): void
    {
        EmployeeProjectAccess::guard($task->t_project_id, $userId);

        if ($task->isUnAssigned()) {
            throw new \DomainException('Task not assigned to any user.');
        }

        if ($task->isAssigned($userId)) {
            throw new \DomainException('Task is already assigned with this user.');
        }

        if (!($task->isProcessing() || $task->isEscalated())) {
            throw new \DomainException('Current status is denied.');
        }
    }

    /**
     * @param QaTask $task
     * @throws ForbiddenHttpException
     */
    public static function permissionGuard(QaTask $task): void
    {
        if (!\Yii::$app->user->can('qa-task/qa-task-action/take-over', ['task' => $task])) {
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
