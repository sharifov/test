<?php

namespace modules\qaTask\src\useCases\qaTask\returnTask\toEscalate;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use modules\qaTask\src\useCases\qaTask\returnTask\QaTaskReturnEvent;
use sales\access\EmployeeProjectAccess;
use sales\dispatchers\EventDispatcher;
use sales\repositories\user\UserRepository;
use yii\web\ForbiddenHttpException;

/**
 * Class QaTaskReturnToEscalateService
 *
 * @property QaTaskRepository $taskRepository
 * @property UserRepository $userRepository
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskReturnToEscalateService
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

    public function return(QaTaskReturnToEscalateForm $form): void
    {
        $task = $this->taskRepository->find($form->getTaskId());
        $user = $this->userRepository->find($form->getUserId());

        self::businessGuard($task, $user->id);

        $startStatusId = $task->t_status_id;
        $oldAssignedUserId = $task->t_assigned_user_id;

        if (!$task->isEscalated()) {
            $task->escalated();
        }

        if (!$task->isUnAssigned()) {
            $task->unAssign();
        }

        $this->taskRepository->save($task);

        $stateLog = new CreateDto(
            $task->t_id,
            $startStatusId,
            $task->t_status_id,
            $form->reasonId,
            $form->description,
            QaTaskActions::RETURN,
            $task->t_assigned_user_id,
            $user->id
        );

        $this->eventDispatcher->dispatchAll([
            new QaTaskReturnToEscalateEvent(
                $task,
                $oldAssignedUserId,
                $stateLog
            ),
            new QaTaskReturnEvent(
                $task,
                $oldAssignedUserId,
                $stateLog
            )
        ]);
    }

    private static function businessGuard(QaTask $task, int $userId): void
    {
        EmployeeProjectAccess::guard($task->t_project_id, $userId);

        if ($task->isPending()) {
            throw new \DomainException('Task is pending.');
        }

        if ($task->isProcessing()) {
            throw new \DomainException('Task is processing.');
        }
    }

    /**
     * @param QaTask $task
     * @throws ForbiddenHttpException
     */
    public static function permissionGuard(QaTask $task): void
    {
        if (!\Yii::$app->user->can('qa-task/qa-task-action/return', ['task' => $task])) {
            throw new ForbiddenHttpException('Access denied.');
        }

        if (!\Yii::$app->user->can('qa-task/qa-task-action/return_To_Escalate')) {
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
