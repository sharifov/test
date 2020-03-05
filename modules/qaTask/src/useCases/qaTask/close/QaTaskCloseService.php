<?php

namespace modules\qaTask\src\useCases\qaTask\close;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use modules\qaTask\src\useCases\qaTask\QaTaskActionsService;
use yii\web\ForbiddenHttpException;

/**
 * Class QaTaskCloseService
 */
class QaTaskCloseService extends QaTaskActionsService
{
    public function close(QaTaskCloseForm $form): void
    {
        $task = $this->taskRepository->find($form->getTaskId());
        $user = $this->userRepository->find($form->getUserId());

        $this->businessGuard($user, $task);

        $startStatusId = $task->t_status_id;

        $task->closed();
        $task->changeRating($form->rating);
        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskCloseEvent(
            $task,
            new CreateDto(
                $task,
                $startStatusId,
                $task->t_status_id,
                null,
                $form->description,
                QaTaskActions::CLOSE,
                $task->t_assigned_user_id,
                $user->id
            )
        ));
    }

    private function businessGuard(Employee $user, QaTask $task): void
    {
        $this->projectAccessService->guard($user, $task->t_project_id);

        if (!$task->isProcessing()) {
            throw new \DomainException('Task must be in processing.');
        }

        if (!$task->isAssigned($user->id)) {
            throw new \DomainException('User must be assigned.');
        }
    }

    public function permissionGuard($userId): void
    {
        if (!$this->accessChecker->checkAccess($userId, 'qa-task/qa-task-action/close')) {
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
