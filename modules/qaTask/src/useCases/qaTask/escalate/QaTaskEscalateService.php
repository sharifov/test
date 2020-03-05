<?php

namespace modules\qaTask\src\useCases\qaTask\escalate;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use modules\qaTask\src\useCases\qaTask\QaTaskActionsService;
use yii\web\ForbiddenHttpException;

/**
 * Class QaTaskCloseService
 */
class QaTaskEscalateService extends QaTaskActionsService
{
    public function escalate(QaTaskEscalateForm $form): void
    {
        $task = $this->taskRepository->find($form->getTaskId());
        $user = $this->userRepository->find($form->getUserId());

        $this->businessGuard($user, $task);

        $startStatusId = $task->t_status_id;

        $task->escalated();
        $task->changeRating($form->rating);
        $task->unAssign();
        $this->taskRepository->save($task);

        $this->eventDispatcher->dispatch(new QaTaskEscalateEvent(
            $task,
            new CreateDto(
                $task,
                $startStatusId,
                $task->t_status_id,
                $form->reasonId,
                $form->description,
                QaTaskActions::ESCALATE,
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
        if (!$this->accessChecker->checkAccess($userId, 'qa-task/qa-task-action/escalate')) {
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
