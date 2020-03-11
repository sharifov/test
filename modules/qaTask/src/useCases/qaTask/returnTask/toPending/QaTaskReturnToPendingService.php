<?php

namespace modules\qaTask\src\useCases\qaTask\returnTask\toPending;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use modules\qaTask\src\useCases\qaTask\QaTaskActionsService;
use modules\qaTask\src\useCases\qaTask\returnTask\QaTaskReturnEvent;
use yii\web\ForbiddenHttpException;

/**
 * Class QaTaskReturnToPendingService
 */
class QaTaskReturnToPendingService extends QaTaskActionsService
{
    public function return(QaTaskReturnToPendingForm $form): void
    {
        $task = $this->taskRepository->find($form->getTaskId());
        $user = $this->userRepository->find($form->getUserId());

        $this->businessGuard($user, $task);

        $startStatusId = $task->t_status_id;
        $oldAssignedUserId = $task->t_assigned_user_id;

        $task->pending();

        if (!$task->isUnAssigned()) {
            $task->unAssign();
        }

        $this->taskRepository->save($task);

        $stateLog = new CreateDto(
            $task,
            $startStatusId,
            $task->t_status_id,
            $form->reasonId,
            $form->description,
            QaTaskActions::RETURN,
            $task->t_assigned_user_id,
            $user->id
        );

        $this->eventDispatcher->dispatchAll([
            new QaTaskReturnToPendingEvent(
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

    private function businessGuard(Employee $user, QaTask $task): void
    {
        $this->projectAccessService->guard($user->getAccess(), $task->t_project_id);

        if ($task->isPending()) {
            throw new \DomainException('Task is pending.');
        }
    }

    public function permissionGuard($userId, QaTask $task): void
    {
        if (!$this->accessChecker->checkAccess($userId, 'qa-task/qa-task-action/return', ['task' => $task])) {
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
