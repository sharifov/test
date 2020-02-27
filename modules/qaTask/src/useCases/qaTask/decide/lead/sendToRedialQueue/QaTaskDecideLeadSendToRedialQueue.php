<?php

namespace modules\qaTask\src\useCases\qaTask\decide\lead\sendToRedialQueue;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\useCases\qaTask\decide\QaTaskDecideService;
use sales\dispatchers\EventDispatcher;
use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;
use sales\services\lead\qcall\QCallService;
use sales\services\TransactionManager;

/**
 * Class QaTaskCloseService
 *
 * @property QaTaskRepository $taskRepository
 * @property UserRepository $userRepository
 * @property EventDispatcher $eventDispatcher
 * @property QaTaskDecideService $decideService
 * @property TransactionManager $transactionManager
 * @property LeadRepository $leadRepository
 * @property QCallService $qCallService
 */
class QaTaskDecideLeadSendToRedialQueue
{
    private $taskRepository;
    private $userRepository;
    private $eventDispatcher;
    private $decideService;
    private $transactionManager;
    private $leadRepository;
    private $qCallService;

    public function __construct(
        QaTaskRepository $taskRepository,
        UserRepository $userRepository,
        EventDispatcher $eventDispatcher,
        QaTaskDecideService $decideService,
        TransactionManager $transactionManager,
        LeadRepository $leadRepository,
        QCallService $qCallService
    )
    {
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->decideService = $decideService;
        $this->transactionManager = $transactionManager;
        $this->leadRepository = $leadRepository;
        $this->qCallService = $qCallService;
    }

    public function handle(int $taskId, int $userId): void
    {
        $task = $this->taskRepository->find($taskId);

        self::businessGuard($task);

        $this->transactionManager->wrap(function () use ($task, $userId) {

            $this->decideService->decide($task->t_id, $userId, 'Sent to Redial');

            $lead = $this->leadRepository->find($task->t_object_id);

            if ($this->qCallService->isExist($lead->id)) {
                return;
            }

            $this->qCallService->createByDefault($lead);
        });
    }

    private static function businessGuard(QaTask $task): void
    {
        if (!QaTaskObjectType::isLead($task->t_object_type_id)) {
            throw new \DomainException('Task Type must be Lead.');
        }
    }

    public static function can(QaTask $task, int $userId): bool
    {
        try {
            QaTaskDecideService::can($task, $userId);
            self::businessGuard($task);
        } catch (\Throwable $e) {
            return false;
        }
        return true;
    }
}
