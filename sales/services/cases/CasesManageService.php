<?php

namespace sales\services\cases;

use sales\entities\cases\Cases;
use sales\entities\cases\CasesCategory;
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
     * For user
     *
     * @param int $caseId
     * @param int $userId
     */
    public function take(int $caseId, int $userId): void
    {
        $case = $this->casesRepository->find($caseId);
        if ($case->isProcessing()) {
            throw new \DomainException('Case is already processing. You can make only take over');
        }
        $user = $this->userRepository->find($userId);
        $case->processing($user->id);
        $this->casesRepository->save($case);
    }

    /**
     * For user
     *
     * @param string $caseGid
     * @param int $userId
     */
    public function takeByGid(string $caseGid, int $userId): void
    {
        $case = $this->casesRepository->findByGid($caseGid);
        if ($case->isProcessing()) {
            throw new \DomainException('Case is already processing. You can make only take over');
        }
        $user = $this->userRepository->find($userId);
        $case->processing($user->id);
        $this->casesRepository->save($case);
    }

    /**
     * For user
     *
     * @param int $caseId
     * @param int $userId
     */
    public function takeOver(int $caseId, int $userId): void
    {
        $case = $this->casesRepository->find($caseId);
        if (!$case->isProcessing()) {
            throw new \DomainException('Case must be in processing');
        }
        $user = $this->userRepository->find($userId);
        $case->processing($user->id);
        $this->casesRepository->save($case);
    }

    /**
     * For user
     *
     * @param string $caseGid
     * @param int $userId
     */
    public function takeOverByGid(string $caseGid, int $userId): void
    {
        $case = $this->casesRepository->findByGid($caseGid);
        if (!$case->isProcessing()) {
            throw new \DomainException('Case must be in processing');
        }
        $user = $this->userRepository->find($userId);
        $case->processing($user->id);
        $this->casesRepository->save($case);
    }

    /**
     * @param int $caseId
     */
    public function pending(int $caseId): void
    {
        $case = $this->casesRepository->find($caseId);
        $case->pending();
        $this->casesRepository->save($case);
    }

    /**
     * @param int $caseId
     */
    public function followUp(int $caseId): void
    {
        $case = $this->casesRepository->find($caseId);
        $case->followUp();
        $this->casesRepository->save($case);
    }

    /**
     * @param int $caseId
     */
    public function solved(int $caseId): void
    {
        $case = $this->casesRepository->find($caseId);
        $case->solved();
        $this->casesRepository->save($case);
    }

    /**
     * @param int $caseId
     */
    public function trash(int $caseId): void
    {
        $case = $this->casesRepository->find($caseId);
        $case->trash();
        $this->casesRepository->save($case);
    }

    /**
     * @param int $caseId
     * @param string $category
     */
    public function updateCategoryByCaseId(int $caseId, string $category): void
    {
        $case = $this->casesRepository->find($caseId);
        $this->updateCategory($case, $category);
    }


    /**
     * @param Cases $case
     * @param string $category
     */
    public function updateCategory(Cases $case, string $category): void
    {
        $category = $this->casesCategoryRepository->findByKey($category);
        $case->updateCategory($category->cc_key);
        $this->casesRepository->save($case);
    }

}