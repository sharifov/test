<?php

namespace modules\qaTask\src\useCases\qaTask\decide;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use sales\access\EmployeeProjectAccess;
use sales\dispatchers\EventDispatcher;
use sales\repositories\user\UserRepository;
use yii\web\ForbiddenHttpException;

/**
 * Class QaTaskDecideService
 *
 * @property QaTaskRepository $taskRepository
 * @property UserRepository $userRepository
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskDecideService
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

    public function decide(int $taskId, int $userId, ?string $description): void
    {
        $task = $this->taskRepository->find($taskId);
        $user = $this->userRepository->find($userId);

        self::businessGuard($task, $user->id);

        $startStatusId = $task->t_status_id;

        $task->closed();

        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskDecideEvent(
            $task,
            new CreateDto(
                $task,
                $startStatusId,
                $task->t_status_id,
               null,
                $description,
                QaTaskActions::DECIDE,
                $task->t_assigned_user_id,
                $user->id
            )
        ));
    }

    private static function businessGuard(QaTask $task, int $userId): void
    {
        EmployeeProjectAccess::guard($task->t_project_id, $userId);

        if (!$task->isEscalated()) {
            throw new \DomainException('Task must be in Escalated.');
        }

        if (!$task->isAssigned($userId)) {
            throw new \DomainException('User must be assigned.');
        }
    }

    /**
     * @throws ForbiddenHttpException
     */
    public static function permissionGuard(): void
    {
        if (!\Yii::$app->user->can('qa-task/qa-task-action/decide')) {
            throw new ForbiddenHttpException('Access denied.');
        }
    }

    public static function can(QaTask $task, int $userId): bool
    {
        try {
            self::permissionGuard();
            self::businessGuard($task, $userId);
        } catch (\Throwable $e) {
            return false;
        }
        return true;
    }
}
