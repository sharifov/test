<?php

namespace sales\services\cases;

use sales\repositories\cases\CasesRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;

/**
 * Class CasesManageService
 * @property CasesRepository $casesRepository
 * @property UserRepository $userRepository
 * @property LeadRepository $leadRepository
 */
class CasesManageService
{

    private $casesRepository;
    private $userRepository;
    private $leadRepository;

    /**
     * CasesManageService constructor.
     * @param CasesRepository $casesRepository
     * @param UserRepository $userRepository
     * @param LeadRepository $leadRepository
     */
    public function __construct(
        CasesRepository $casesRepository,
        UserRepository $userRepository,
        LeadRepository $leadRepository
    )
    {
        $this->casesRepository = $casesRepository;
        $this->userRepository = $userRepository;
        $this->leadRepository = $leadRepository;
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

}