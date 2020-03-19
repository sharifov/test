<?php

namespace sales\services\cases;

use common\models\Employee;
use common\models\Lead;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeProjectAccess;
use sales\entities\cases\Cases;
use sales\repositories\cases\CaseCategoryRepository;
use sales\repositories\cases\CasesRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;
use sales\services\ServiceFinder;

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
    )
    {
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
    public function take(int $caseId, int $userId, ?int $creatorId, ?string $description = ''): void
    {
        $this->processing($caseId, $userId, $creatorId, $description);
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
    }

    /**
     * @param int|Cases $case
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function pending($case, ?int $creatorId, ?string $description = ''): void
    {
        $case = $this->finder->caseFind($case);
        $case->pending($creatorId, $description);
        $this->casesRepository->save($case);
    }

    /**
     * @param int|Cases $case
     * @param int|null $creatorId
     * @param string|null $description
     * @param string|null $deadline
     */
    public function followUp($case, ?int $creatorId, ?string $description, ?string $deadline): void
    {
        $case = $this->finder->caseFind($case);
        $case->followUp($creatorId, $description);
        if ($deadline !== null) {
            $case->setDeadline($deadline);
        }
        $this->casesRepository->save($case);
    }

    /**
     * @param int|Cases $case
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function solved($case, ?int $creatorId, ?string $description = ''): void
    {
        $case = $this->finder->caseFind($case);
        $case->solved($creatorId, $description);
        $this->casesRepository->save($case);
    }

    /**
     * @param int|Cases $case
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function trash($case, ?int $creatorId, ?string $description = ''): void
    {
        $case = $this->finder->caseFind($case);
        $case->trash($creatorId, $description);
        $this->casesRepository->save($case);
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
