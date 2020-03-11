<?php

namespace modules\qaTask\src\useCases\qaTask\cancel;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use modules\qaTask\src\useCases\qaTask\QaTaskActionsService;
use yii\web\ForbiddenHttpException;

/**
 * Class QaTaskCancelService
 */
class QaTaskCancelService extends QaTaskActionsService
{
    public function cancel(QaTaskCancelForm $form): void
    {
        $task = $this->taskRepository->find($form->getTaskId());
        $user = $this->userRepository->find($form->getUserId());

        $this->businessGuard($user, $task);

        $startStatusId = $task->t_status_id;

        $task->canceled();
        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskCancelEvent(
            $task,
            new CreateDto(
                $task,
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

    private function businessGuard(Employee $user, QaTask $task): void
    {
        $this->projectAccessService->guard($user->getAccess(), $task->t_project_id);

        if ($task->isCanceled()) {
            throw new \DomainException('Task is already is canceled.');
        }
    }

    public function permissionGuard($userId, QaTask $task): void
    {
        if (!$this->accessChecker->checkAccess($userId, 'qa-task/qa-task-action/cancel', ['task' => $task])) {
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
