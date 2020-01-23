<?php

namespace sales\services\cases;

use common\models\Employee;
use common\models\Lead;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeProjectAccess;
use sales\entities\cases\Cases;
use sales\repositories\cases\CasesCategoryRepository;
use sales\repositories\cases\CasesRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;
use sales\services\ServiceFinder;

/**
 * Class CasesManageService
 * @property CasesRepository $casesRepository
 * @property UserRepository $userRepository
 * @property LeadRepository $leadRepository
 * @property CasesCategoryRepository $casesCategoryRepository
 * @property ServiceFinder $finder
 */
class CasesManageService
{

    private $casesRepository;
    private $userRepository;
    private $leadRepository;
    private $casesCategoryRepository;
    private $finder;

    /**
     * CasesManageService constructor.
     * @param CasesRepository $casesRepository
     * @param UserRepository $userRepository
     * @param LeadRepository $leadRepository
     * @param CasesCategoryRepository $casesCategoryRepository
     * @param ServiceFinder $finder
     */
    public function __construct(
        CasesRepository $casesRepository,
        UserRepository $userRepository,
        LeadRepository $leadRepository,
        CasesCategoryRepository $casesCategoryRepository,
        ServiceFinder $finder
    )
    {
        $this->casesRepository = $casesRepository;
        $this->userRepository = $userRepository;
        $this->leadRepository = $leadRepository;
        $this->casesCategoryRepository = $casesCategoryRepository;
        $this->finder = $finder;
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
     */
    public function followUp($case, ?int $creatorId, ?string $description = ''): void
    {
        $case = $this->finder->caseFind($case);
        $case->followUp($creatorId, $description);
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
     * @param string $categoryKey
     */
    public function updateCategoryByCaseId(int $caseId, string $categoryKey): void
    {
        $case = $this->casesRepository->find($caseId);
        $this->updateCategory($case, $categoryKey);
    }

    /**
     * @param Cases $case
     * @param string $categoryKey
     */
    public function updateCategory(Cases $case, string $categoryKey): void
    {
        $category = $this->casesCategoryRepository->findByKey($categoryKey);
        $case->updateCategory($category->cc_key);
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
