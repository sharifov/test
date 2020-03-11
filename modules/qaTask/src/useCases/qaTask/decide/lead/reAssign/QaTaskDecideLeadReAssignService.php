<?php

namespace modules\qaTask\src\useCases\qaTask\decide\lead\reAssign;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\useCases\qaTask\decide\QaTaskDecideService;
use modules\qaTask\src\useCases\qaTask\QaTaskActionsService;
use sales\access\EmployeeAccess;
use sales\access\ProjectAccessService;
use sales\dispatchers\EventDispatcher;
use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;
use sales\services\TransactionManager;
use yii\rbac\CheckAccessInterface;

/**
 * Class QaTaskDecideLeadReAssignService
 *
 * @property QaTaskDecideService $decideService
 * @property LeadRepository $leadRepository
 */
class QaTaskDecideLeadReAssignService extends QaTaskActionsService
{
    protected $decideService;
    protected $leadRepository;

    public function __construct(
        QaTaskRepository $taskRepository,
        UserRepository $userRepository,
        EventDispatcher $eventDispatcher,
        ProjectAccessService $projectAccessService,
        CheckAccessInterface $accessChecker,
        TransactionManager $transactionManager,
        QaTaskDecideService $decideService,
        LeadRepository $leadRepository
    )
    {
        parent::__construct($taskRepository, $userRepository, $eventDispatcher, $projectAccessService, $accessChecker, $transactionManager);
        $this->decideService = $decideService;
        $this->leadRepository = $leadRepository;
    }

    public function handle(QaTaskDecideLeadReAssignForm $form): void
    {
        $task = $this->taskRepository->find($form->getTaskId());

        $this->businessGuard($task);

        $this->transactionManager->wrap(function () use ($task, $form) {

            $this->decideService->decide($task->t_id, $form->getUserId(), 'Re-assigned');

            $lead = $this->leadRepository->find($task->t_object_id);
            $assignUser = $this->userRepository->find($form->assignUserId);

            EmployeeAccess::leadAccess($lead, $assignUser);

            $lead->processing($assignUser->id, $form->getUserId(), 'Re-assign (QA)');
            $this->leadRepository->save($lead);

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
