<?php

namespace modules\qaTask\src\useCases\qaTask\decide;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use modules\qaTask\src\useCases\qaTask\QaTaskActionsService;
use yii\web\ForbiddenHttpException;

/**
 * Class QaTaskDecideService
 */
class QaTaskDecideService extends QaTaskActionsService
{
    public function decide(int $taskId, int $userId, ?string $description): void
    {
        $task = $this->taskRepository->find($taskId);
        $user = $this->userRepository->find($userId);

        $this->businessGuard($user, $task);

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

    private function businessGuard(Employee $user, QaTask $task): void
    {
        $this->projectAccessService->guard($user, $task->t_project_id);

        if (!$task->isEscalated()) {
            throw new \DomainException('Task must be in Escalated.');
        }

        if (!$task->isAssigned($user->id)) {
            throw new \DomainException('User must be assigned.');
        }
    }

    public function permissionGuard($userId): void
    {
        if (!$this->accessChecker->checkAccess($userId, 'qa-task/qa-task-action/decide')) {
            throw new ForbiddenHttpException('Access denied.');
        }
    }

    public static function can(Employee $user, QaTask $task): bool
    {
        $service = \Yii::createObject(static::class);
        try {
            $service->permissionGuard($user->id);
            $service->businessGuard($user, $task);
        } catch (\Throwable $e) {
            return false;
        }
        return true;
    }
}
