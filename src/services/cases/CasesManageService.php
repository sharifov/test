<?php

namespace src\services\cases;

use common\models\Employee;
use common\models\Lead;
use src\access\EmployeeDepartmentAccess;
use src\access\EmployeeProjectAccess;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\entities\cases\CasesSourceType;
use src\entities\cases\CasesStatus;
use src\repositories\cases\CaseCategoryRepository;
use src\repositories\cases\CasesRepository;
use src\repositories\lead\LeadRepository;
use src\repositories\user\UserRepository;
use src\dispatchers\EventDispatcher;
use src\entities\cases\events\CasesTakeOverEvent;
use src\entities\cases\events\CasesManualChangeStatusProcessingEvent;
use src\services\ServiceFinder;
use Yii;

/**
 * Class CasesManageService
 * @property CasesRepository $casesRepository
 * @property UserRepository $userRepository
 * @property LeadRepository $leadRepository
 * @property CaseCategoryRepository $caseCategoryRepository
 * @property ServiceFinder $finder
 */
class CasesManageService
{
    private $casesRepository;
    private $userRepository;
    private $leadRepository;
    private $caseCategoryRepository;
    private $finder;

    /**
     * CasesManageService constructor.
     * @param CasesRepository $casesRepository
     * @param UserRepository $userRepository
     * @param LeadRepository $leadRepository
     * @param CaseCategoryRepository $caseCategoryRepository
     * @param ServiceFinder $finder
     */
    public function __construct(
        CasesRepository $casesRepository,
        UserRepository $userRepository,
        LeadRepository $leadRepository,
        CaseCategoryRepository $caseCategoryRepository,
        ServiceFinder $finder
    ) {
        $this->casesRepository = $casesRepository;
        $this->userRepository = $userRepository;
        $this->leadRepository = $leadRepository;
        $this->caseCategoryRepository = $caseCategoryRepository;
        $this->finder = $finder;
    }

    public function needAction(int $caseId): void
    {
        try {
            $case = $this->casesRepository->find($caseId);
            if ($case->isNeedAction()) {
                return;
            }
            $case->onNeedAction();
            $this->casesRepository->save($case);
        } catch (\Throwable $e) {
            \Yii::error($e, 'CasesManageService:needAction');
        }
    }

    public function markChecked(int $caseId): void
    {
        $case = $this->casesRepository->find($caseId);
        $case->offNeedAction();
        $this->casesRepository->save($case);
    }

    /**
     * @param int|Cases $case
     * @param int|Lead $lead
     */
    public function assignLead($case, $lead): void
    {
        $case = $this->finder->caseFind($case);
        $lead = $this->finder->leadFind($lead);
        $case->assignLead($lead->id);
        $this->casesRepository->save($case);
    }

    /**
     * @param int $caseId
     * @param int $userId
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function callAutoTake(int $caseId, int $userId, ?int $creatorId, ?string $description = ''): void
    {
        $case = $this->finder->caseFind($caseId);
        $user = $this->finder->userFind($userId);

        if (!($case->isPending() && $case->isFreedOwner())) {
            throw new \DomainException('Case could not be call auto take now.');
        }

        $this->processing($case, $user, $creatorId, $description);
    }

    /**
     * @param int $caseId
     * @param int $userId
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function take(int $caseId, int $userId, ?int $creatorId, ?string $description = ''): void
    {
        $case = $this->finder->caseFind($caseId);
        $user = $this->finder->userFind($userId);

        /*if (!($case->isPending() || $case->isFollowUp() || $case->isTrash())) {
            throw new \DomainException('Case could not be taken now.');
        }*/

        $this->processing($case, $user, $creatorId, $description);
    }

    /**
     * @param int $caseId
     * @param int $userId
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function takeOver(int $caseId, int $userId, ?int $creatorId, ?string $description = ''): void
    {
        $case = $this->finder->caseFind($caseId);
        $user = $this->finder->userFind($userId);

        if (!$case->isProcessing()) {
            throw new \DomainException('Case is not Processing.');
        }

        if ($case->isOwner($user->id)) {
            throw new \DomainException('User already assigned.');
        }

        $this->processing($caseId, $userId, $creatorId, $description);
        $eventDispatcher = Yii::createObject(EventDispatcher::class);
        $eventDispatcher->dispatch(new CasesTakeOverEvent($case, $case->cs_user_id, $userId), 'CasesTakeOverEvent_' . $case->cs_id);
    }

    /**
     * @param string $caseGid
     * @param int $userId
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function takeByGid(string $caseGid, int $userId, ?int $creatorId, ?string $description = ''): void
    {
        $case = $this->casesRepository->findByGid($caseGid);
        $this->processing($case->cs_id, $userId, $creatorId, $description);
    }

    /**
     * @param int|Cases $case
     * @param int|Employee $user
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function processing($case, $user, ?int $creatorId, ?string $description = ''): void
    {
        $case = $this->finder->caseFind($case);
        $user = $this->finder->userFind($user);
        $this->guardAccessUserToCase($case, $user);
        $case->processing($user->id, $creatorId, $description);
        $this->casesRepository->save($case);

        if ($creatorId) {
            $creator = $this->finder->userFind($creatorId);
        }
        $eventDescription = 'Case status changed to ' . CasesStatus::STATUS_LIST[$case->cs_status];
        $eventDescription .= ' By: ' . ($creator->username ?? 'System.');
        $eventDescription .= ($description ? ' Reason: ' . $description : '');
        $case->addEventLog(CaseEventLog::CASE_STATUS_CHANGED, $eventDescription);
    }

    public function manualChangeStatusProcessing($caseId, $userId, int $creatorId, ?string $description = ''): void
    {
        $case = $this->finder->caseFind($caseId);
        $this->processing($caseId, $userId, $creatorId, $description);
        $eventDispatcher = Yii::createObject(EventDispatcher::class);
        $eventDispatcher->dispatch(new CasesManualChangeStatusProcessingEvent($case, $case->cs_user_id, $userId, $creatorId), 'CasesManualChangeStatusProcessingEvent_' . $case->cs_id);
    }

    /**
     * @param int|Cases $case
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function pending($case, ?int $creatorId, ?string $description = '', ?string $username = null): void
    {
        $case = $this->finder->caseFind($case);
        $case->pending($creatorId, $description);
        $this->casesRepository->save($case);
        $case->addEventLog(CaseEventLog::CASE_STATUS_CHANGED, 'Case status changed to ' . CasesStatus::STATUS_LIST[$case->cs_status] . ' By: ' . ($username ?? 'System.') . ($description ? ' Reason: ' . $description : ''));
    }

    /**
     * @param int|Cases $case
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function awaiting($case, ?int $creatorId, ?string $description = '', ?string $username = null): void
    {
        $case = $this->finder->caseFind($case);
        $case->awaiting($creatorId, $description);
        $this->casesRepository->save($case);
        $case->addEventLog(CaseEventLog::CASE_STATUS_CHANGED, 'Case status changed to ' . CasesStatus::STATUS_LIST[$case->cs_status] . ' By: ' . ($username ?? 'System.') . ($description ? ' Reason: ' . $description : ''));
    }

    /**
     * @param int|Cases $case
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function new($case, ?int $creatorId, ?string $description = '', ?string $username = null): void
    {
        $case = $this->finder->caseFind($case);
        $case->new($creatorId, $description);
        $this->casesRepository->save($case);
        $case->addEventLog(CaseEventLog::CASE_STATUS_CHANGED, 'Case status changed to ' . CasesStatus::STATUS_LIST[$case->cs_status] . ' By: ' . ($username ?? 'System.') . ($description ? ' Reason: ' . $description : ''));
    }

    /**
     * @param int|Cases $case
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function autoProcessing($case, ?int $creatorId, ?string $description = '', ?string $username = null): void
    {
        $case = $this->finder->caseFind($case);
        $case->autoProcessing($creatorId, $description);
        $this->casesRepository->save($case);
        $case->addEventLog(CaseEventLog::CASE_STATUS_CHANGED, 'Case status changed to ' . CasesStatus::STATUS_LIST[$case->cs_status] . ' By: ' . ($username ?? 'System.') . ($description ? ' Reason: ' . $description : ''));
    }

    /**
     * @param int|Cases $case
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function error($case, ?int $creatorId, ?string $description = '', ?string $username = null): void
    {
        $case = $this->finder->caseFind($case);
        $case->error($creatorId, $description);
        $this->casesRepository->save($case);
        $case->addEventLog(CaseEventLog::CASE_STATUS_CHANGED, 'Case status changed to ' . CasesStatus::STATUS_LIST[$case->cs_status] . ' By: ' . ($username ?? 'System.') . ($description ? ' Reason: ' . $description : ''));
    }

    /**
     * @param int|Cases $case
     * @param int|null $creatorId
     * @param string|null $description
     * @param string|null $deadline
     */
    public function followUp($case, ?int $creatorId, ?string $description, ?string $deadline, ?string $username = null): void
    {
        $case = $this->finder->caseFind($case);
        $case->followUp($creatorId, $description);
        if ($deadline !== null) {
            $case->setDeadline($deadline);
        }
        $this->casesRepository->save($case);
        $case->addEventLog(CaseEventLog::CASE_STATUS_CHANGED, 'Case status changed to ' . CasesStatus::STATUS_LIST[$case->cs_status] . ' By: ' . ($username ?? 'System.') . ($description ? ' Reason: ' . $description : ''));
    }

    /**
     * @param int|Cases $case
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function solved($case, ?int $creatorId, ?string $description = '', ?string $username = null): void
    {
        $case = $this->finder->caseFind($case);
        $case->solved($creatorId, $description);
        $this->casesRepository->save($case);
        $case->addEventLog(CaseEventLog::CASE_STATUS_CHANGED, 'Case status changed to ' . CasesStatus::STATUS_LIST[$case->cs_status] . ' By: ' . ($username ?? 'System.') . ($description ? ' Reason: ' . $description : ''));
    }

    /**
     * @param int|Cases $case
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function trash($case, ?int $creatorId, ?string $description = '', ?string $username = null): void
    {
        $case = $this->finder->caseFind($case);
        $case->trash($creatorId, $description);
        $this->casesRepository->save($case);
        $case->addEventLog(CaseEventLog::CASE_STATUS_CHANGED, 'Case status changed to ' . CasesStatus::STATUS_LIST[$case->cs_status] . ' By: ' . ($username ?? 'System.') . ($description ? ' Reason: ' . $description : ''));
    }

    /**
     * @param int $caseId
     * @param int $categoryId
     */
    public function updateCategoryByCaseId(int $caseId, int $categoryId): void
    {
        $case = $this->casesRepository->find($caseId);
        $this->updateCategory($case, $categoryId);
    }

    /**
     * @param Cases $case
     * @param int $categoryId
     */
    public function updateCategory(Cases $case, int $categoryId): void
    {
        $category = $this->caseCategoryRepository->find($categoryId);
        $case->updateCategory($category->cc_id);
        $this->casesRepository->save($case);
    }

    /**
     * @param Cases $case
     * @param Employee $user
     */
    private function guardAccessUserToCase(Cases $case, Employee $user): void
    {
        if ($case->cs_dep_id) {
            if (!EmployeeDepartmentAccess::isInDepartment($case->cs_dep_id, $user->id)) {
                throw new \DomainException('This user cannot access to case department');
            }
        }
        if ($case->cs_project_id) {
            if (!EmployeeProjectAccess::isInProject($case->cs_project_id, $user->id)) {
                throw new \DomainException('This user cannot access to case project');
            }
        }
    }
}
