<?php

namespace modules\qaTask\src\useCases\qaTask\takeOver;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use modules\qaTask\src\useCases\qaTask\QaTaskActionsService;
use yii\web\ForbiddenHttpException;

/**
 * Class QaTaskActionTakeOverService
 */
class QaTaskTakeOverService extends QaTaskActionsService
{
    public function takeOver(QaTaskTakeOverForm $form): void
    {
        $task = $this->taskRepository->find($form->getTaskId());
        $user = $this->userRepository->find($form->getUserId());

        $this->businessGuard($user, $task);

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
                $task,
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

    private function businessGuard(Employee $user, QaTask $task): void
    {
        $this->projectAccessService->guard($user->getAccess(), $task->t_project_id);

        if ($task->isUnAssigned()) {
            throw new \DomainException('Task not assigned to any user.');
        }

        if ($task->isAssigned($user->id)) {
            throw new \DomainException('Task is already assigned with this user.');
        }

        if (!($task->isProcessing() || $task->isEscalated())) {
            throw new \DomainException('Current status is denied.');
        }
    }

    public function permissionGuard($userId, QaTask $task): void
    {
        if (!$this->accessChecker->checkAccess($userId, 'qa-task/qa-task-action/take-over', ['task' => $task])) {
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
