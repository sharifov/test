<?php

namespace sales\services\cases;

use sales\entities\cases\Cases;
use sales\repositories\cases\CasesCategoryRepository;
use sales\repositories\cases\CasesRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;

/**
 * Class CasesManageService
 * @property CasesRepository $casesRepository
 * @property UserRepository $userRepository
 * @property LeadRepository $leadRepository
 * @property CasesCategoryRepository $casesCategoryRepository
 */
class CasesManageService
{

    private $casesRepository;
    private $userRepository;
    private $leadRepository;
    private $casesCategoryRepository;

    /**
     * CasesManageService constructor.
     * @param CasesRepository $casesRepository
     * @param UserRepository $userRepository
     * @param LeadRepository $leadRepository
     * @param CasesCategoryRepository $casesCategoryRepository
     */
    public function __construct(
        CasesRepository $casesRepository,
        UserRepository $userRepository,
        LeadRepository $leadRepository,
        CasesCategoryRepository $casesCategoryRepository
    )
    {
        $this->casesRepository = $casesRepository;
        $this->userRepository = $userRepository;
        $this->leadRepository = $leadRepository;
        $this->casesCategoryRepository = $casesCategoryRepository;
    }

    /**
     * @param int $caseId
     * @param int $leadId
     */
    public function assignLead(int $caseId, int $leadId): void
    {
        $case = $this->casesRepository->find($caseId);
        $lead = $this->leadRepository->find($leadId);
        $case->assignLead($lead->id);
        $this->casesRepository->save($case);
    }
    
    /**
     * For system
     *
     * @param int $caseId
     * @param int $userId
     */
    public function assignUser(int $caseId, int $userId): void
    {
        $case = $this->casesRepository->find($caseId);
        $user = $this->userRepository->find($userId);
        $case->processing($user->id);
        $this->casesRepository->save($case);
    }

    /**
     * @param int $caseId
     * @param int $userId
     * @param string $description
     */
    public function take(int $caseId, int $userId, string $description = ''): void
    {
        $case = $this->casesRepository->find($caseId);
        $user = $this->userRepository->find($userId);
        $this->processing($case, $user->id, $description);
    }

    /**
     * @param string $caseGid
     * @param int $userId
     * @param string $description
     */
    public function takeByGid(string $caseGid, int $userId, string $description = ''): void
    {
        $case = $this->casesRepository->findByGid($caseGid);
        $user = $this->userRepository->find($userId);
        $this->processing($case, $user->id, $description);
    }

    /**
     * @param Cases $case
     * @param int $userId
     * @param string $description
     */
    public function processing(Cases $case, int $userId, string $description = ''): void
    {
        $case->processing($userId, $description);
        $this->casesRepository->save($case);
    }

    /**
     * @param int $caseId
     * @param string $description
     */
    public function pending(int $caseId, string $description = ''): void
    {
        $case = $this->casesRepository->find($caseId);
        $case->pending($description);
        $this->casesRepository->save($case);
    }

    /**
     * @param int $caseId
     * @param string $description
     */
    public function followUp(int $caseId, string $description = ''): void
    {
        $case = $this->casesRepository->find($caseId);
        $case->followUp($description);
        $this->casesRepository->save($case);
    }

    /**
     * @param int $caseId
     * @param string $description
     */
    public function solved(int $caseId, string $description = ''): void
    {
        $case = $this->casesRepository->find($caseId);
        $case->solved($description);
        $this->casesRepository->save($case);
    }

    /**
     * @param int $caseId
     * @param string $description
     */
    public function trash(int $caseId, string $description = ''): void
    {
        $case = $this->casesRepository->find($caseId);
        $case->trash($description);
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

}