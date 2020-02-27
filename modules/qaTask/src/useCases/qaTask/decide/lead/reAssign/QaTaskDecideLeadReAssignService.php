<?php

namespace modules\qaTask\src\useCases\qaTask\decide\lead\reAssign;

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\useCases\qaTask\decide\QaTaskDecideService;
use sales\access\EmployeeAccess;
use sales\dispatchers\EventDispatcher;
use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;
use sales\services\TransactionManager;

/**
 * Class QaTaskDecideLeadReAssignService
 *
 * @property QaTaskRepository $taskRepository
 * @property EventDispatcher $eventDispatcher
 * @property QaTaskDecideService $decideService
 * @property TransactionManager $transactionManager
 * @property LeadRepository $leadRepository
 * @property UserRepository $userRepository
 */
class QaTaskDecideLeadReAssignService
{
    private $taskRepository;
    private $eventDispatcher;
    private $decideService;
    private $transactionManager;
    private $leadRepository;
    private $userRepository;

    public function __construct(
        QaTaskRepository $taskRepository,
        EventDispatcher $eventDispatcher,
        QaTaskDecideService $decideService,
        TransactionManager $transactionManager,
        LeadRepository $leadRepository,
        UserRepository $userRepository
    )
    {
        $this->taskRepository = $taskRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->decideService = $decideService;
        $this->transactionManager = $transactionManager;
        $this->leadRepository = $leadRepository;
        $this->userRepository = $userRepository;
    }

    public function handle(QaTaskDecideLeadReAssignForm $form): void
    {
        $task = $this->taskRepository->find($form->getTaskId());

        self::businessGuard($task);

        $this->transactionManager->wrap(function () use ($task, $form) {

            $this->decideService->decide($task->t_id, $form->getUserId(), 'Re-assigned');

            $lead = $this->leadRepository->find($task->t_object_id);
            $assignUser = $this->userRepository->find($form->assignUserId);

            EmployeeAccess::leadAccess($lead, $assignUser);

            $lead->processing($assignUser->id, $form->getUserId(), 'Re-assign (QA)');
            $this->leadRepository->save($lead);

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
