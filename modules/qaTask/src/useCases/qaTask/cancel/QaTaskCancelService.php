<?php

namespace modules\qaTask\src\useCases\qaTask\cancel;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use sales\access\ProjectAccessService;
use sales\auth\Auth;
use sales\dispatchers\EventDispatcher;
use sales\repositories\user\UserRepository;
use yii\web\ForbiddenHttpException;

/**
 * Class QaTaskCancelService
 *
 * @property QaTaskRepository $taskRepository
 * @property UserRepository $userRepository
 * @property EventDispatcher $eventDispatcher
 * @property ProjectAccessService $projectAccessService
 */
class QaTaskCancelService
{
    private $taskRepository;
    private $userRepository;
    private $eventDispatcher;
    private $projectAccessService;

    public function __construct(
        QaTaskRepository $taskRepository,
        UserRepository $userRepository,
        EventDispatcher $eventDispatcher,
        ProjectAccessService $projectAccessService
    )
    {
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->projectAccessService = $projectAccessService;
    }

    public function cancel(QaTaskCancelForm $form): void
    {
        $task = $this->taskRepository->find($form->getTaskId());
        $user = $this->userRepository->find($form->getUserId());

        $this->businessGuard($task, $user);

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

    private function businessGuard(QaTask $task, Employee $user): void
    {
        $this->projectAccessService->guard($user, $task->t_project_id);

        if ($task->isCanceled()) {
            throw new \DomainException('Task is already is canceled.');
        }
    }

    /**
     * @param QaTask $task
     * @throws ForbiddenHttpException
     */
    public static function permissionGuard(QaTask $task): void
    {
        if (!Auth::can('qa-task/qa-task-action/cancel', ['task' => $task])) {
            throw new ForbiddenHttpException('Access denied.');
        }
    }

    public static function can(QaTask $task, Employee $user): bool
    {
        $service = \Yii::createObject(static::class);
        try {
            self::permissionGuard($task);
            $service->businessGuard($task, $user);
        } catch (\Throwable $e) {
            return false;
        }
        return true;
    }
}
