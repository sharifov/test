<?php

namespace modules\qaTask\src\useCases\qaTask\take;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use sales\access\EmployeeProjectAccess;
use sales\dispatchers\EventDispatcher;
use sales\repositories\user\UserRepository;
use yii\web\ForbiddenHttpException;

/**
 * Class QaTaskActionTakeService
 *
 * @property QaTaskRepository $taskRepository
 * @property UserRepository $userRepository
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskTakeService
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

        self::businessGuard($task, $user->id);

        $startStatusId = $task->t_status_id;

        if ($task->isPending()) {
            $task->processing();
        }

        $task->assign($user->id);
        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskTakeEvent(
            $task,
            new CreateDto(
                $task->t_id,
                $startStatusId,
                $task->t_status_id,
                null,
                null,
                QaTaskActions::TAKE,
                $task->t_assigned_user_id,
                $user->id
            )
        ));
    }

    private static function businessGuard(QaTask $task, int $userId): void
    {
        EmployeeProjectAccess::guard($task->t_project_id, $userId);

        if (!$task->isUnAssigned()) {
            throw new \DomainException('Task is already assigned.');
        }

        if (!($task->isPending() || $task->isEscalated())) {
            throw new \DomainException('Current status is denied.');
        }
    }

    /**
     * @param QaTask $task
     * @throws ForbiddenHttpException
     */
    public static function permissionGuard(QaTask $task): void
    {
        if (!\Yii::$app->user->can('qa-task/task/take', ['task' => $task])) {
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
