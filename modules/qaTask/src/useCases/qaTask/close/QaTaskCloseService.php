<?php

namespace modules\qaTask\src\useCases\qaTask\close;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use sales\access\EmployeeProjectAccess;
use sales\dispatchers\EventDispatcher;
use sales\repositories\user\UserRepository;
use yii\web\ForbiddenHttpException;

/**
 * Class QaTaskCloseService
 *
 * @property QaTaskRepository $taskRepository
 * @property UserRepository $userRepository
 * @property EventDispatcher $eventDispatcher
 */
class QaTaskCloseService
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

    public function close(QaTaskCloseForm $form): void
    {
        $task = $this->taskRepository->find($form->getTaskId());
        $user = $this->userRepository->find($form->getUserId());

        self::businessGuard($task, $user->id);

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

    private static function businessGuard(QaTask $task, int $userId): void
    {
        EmployeeProjectAccess::guard($task->t_project_id, $userId);

        if (!$task->isProcessing()) {
            throw new \DomainException('Task must be in processing.');
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
        if (!\Yii::$app->user->can('qa-task/qa-task-action/close')) {
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
