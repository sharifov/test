<?php

namespace modules\qaTask\src\useCases\qaTask\decide\lead\sendToRedialQueue;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\useCases\qaTask\decide\QaTaskDecideService;
use modules\qaTask\src\useCases\qaTask\QaTaskActionsService;
use sales\access\ProjectAccessService;
use sales\dispatchers\EventDispatcher;
use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;
use sales\services\lead\qcall\QCallService;
use sales\services\TransactionManager;
use yii\rbac\CheckAccessInterface;

/**
 * Class QaTaskCloseService
 *
 * @property QaTaskDecideService $decideService
 * @property LeadRepository $leadRepository
 * @property QCallService $qCallService
 */
class QaTaskDecideLeadSendToRedialQueue extends QaTaskActionsService
{
    protected $decideService;
    protected $leadRepository;
    protected $qCallService;

    public function __construct(
        QaTaskRepository $taskRepository,
        UserRepository $userRepository,
        EventDispatcher $eventDispatcher,
        ProjectAccessService $projectAccessService,
        CheckAccessInterface $accessChecker,
        TransactionManager $transactionManager,
        QaTaskDecideService $decideService,
        LeadRepository $leadRepository,
        QCallService $qCallService
    )
    {
        parent::__construct($taskRepository, $userRepository, $eventDispatcher, $projectAccessService, $accessChecker, $transactionManager);
        $this->decideService = $decideService;
        $this->leadRepository = $leadRepository;
        $this->qCallService = $qCallService;
    }

    public function handle(int $taskId, int $userId): void
    {
        $task = $this->taskRepository->find($taskId);

        $this->businessGuard($task);

        $this->transactionManager->wrap(function () use ($task, $userId) {

            $this->decideService->decide($task->t_id, $userId, 'Sent to Redial');

            $lead = $this->leadRepository->find($task->t_object_id);

            if ($this->qCallService->isExist($lead->id)) {
                return;
            }

            $this->qCallService->createByDefault($lead);
        });
    }

    private function businessGuard(QaTask $task): void
    {
        if (!QaTaskObjectType::isLead($task->t_object_type_id)) {
            throw new \DomainException('Task Type must be Lead.');
        }
    }

    public static function can(Employee $user, QaTask $task): bool
    {
        $service = \Yii::createObject(static::class);
        try {
            QaTaskDecideService::can($user, $task);
            $service->businessGuard($task);
        } catch (\Throwable $e) {
            return false;
        }
        return true;
    }
}
