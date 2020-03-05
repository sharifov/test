<?php

namespace modules\qaTask\src\useCases\qaTask\take;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use modules\qaTask\src\useCases\qaTask\QaTaskActionsService;
use yii\web\ForbiddenHttpException;

/**
 * Class QaTaskActionTakeService
 */
class QaTaskTakeService extends QaTaskActionsService
{
    public function take(int $taskId, int $userId): void
    {
        $task = $this->taskRepository->find($taskId);
        $user = $this->userRepository->find($userId);

        $this->businessGuard($user, $task);

        $startStatusId = $task->t_status_id;

        if ($task->isPending()) {
            $task->processing();
        }

        $task->assign($user->id);
        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskTakeEvent(
            $task,
            new CreateDto(
                $task,
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

    private function businessGuard(Employee $user, QaTask $task): void
    {
        $this->projectAccessService->guard($user, $task->t_project_id);

        if (!$task->isUnAssigned()) {
            throw new \DomainException('Task is already assigned.');
        }

        if (!($task->isPending() || $task->isEscalated())) {
            throw new \DomainException('Current status is denied.');
        }
    }

    public function permissionGuard($userId, QaTask $task): void
    {
        if (!$this->accessChecker->checkAccess($userId, 'qa-task/qa-task-action/take', ['task' => $task])) {
            throw new ForbiddenHttpException('Access denied.');
        }
    }

    public static function can(Employee $user, QaTask $task): bool
    {
        $service = \Yii::createObject(static::class);
        try {
            $service->permissionGuard($user->id, $task);
            $service->businessGuard($user, $task);
        } catch (\Throwable $e) {
            return false;
        }
        return true;
    }
}
