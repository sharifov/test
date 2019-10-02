<?php

namespace sales\services\lead;

use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;
use sales\services\TransactionManager;

/**
 * Class LeadStateService
 *
 * @property LeadRepository $leadRepository
 * @property UserRepository $userRepository
 * @property TransactionManager $transactionManager
 */
class LeadStateService
{
    private $leadRepository;
    private $userRepository;
    private $transactionManager;

    /**
     * @param LeadRepository $leadRepository
     * @param UserRepository $userRepository
     * @param TransactionManager $transactionManager
     */
    public function __construct(
        LeadRepository $leadRepository,
        UserRepository $userRepository,
        TransactionManager $transactionManager
    )
    {
        $this->leadRepository = $leadRepository;
        $this->userRepository = $userRepository;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param int $leadId
     * @param string|null $description
     */
    public function followUp(int $leadId, ?string $description = ''): void
    {
        $lead = $this->leadRepository->find($leadId);
        $lead->followUp($description);
        $this->leadRepository->save($lead);
    }

    /**
     * @param int $leadId
     * @param string|null $description
     */
    public function trash(int $leadId, ?string $description = ''): void
    {
        $lead = $this->leadRepository->find($leadId);
        $lead->trash($description);
        $this->leadRepository->save($lead);
    }

    /**
     * @param int $leadId
     * @param int $originId
     * @param string|null $description
     */
    public function duplicate(int $leadId, int $originId, ?string $description = ''): void
    {
        $lead = $this->leadRepository->find($leadId);
        $lead->duplicate($originId, $description);
        $this->leadRepository->save($lead);
    }

    /**
     * @param int $leadId
     * @param string|null $snoozeFor
     * @param string|null $description
     */
    public function snooze(int $leadId, ?string $snoozeFor = '', ?string $description = ''): void
    {
        $lead = $this->leadRepository->find($leadId);
        $lead->snooze($snoozeFor, $description);
        $this->leadRepository->save($lead);
    }

    /**
     * @param int $leadId
     * @param string|null $description
     */
    public function reject(int $leadId, ?string $description = ''): void
    {
        $lead = $this->leadRepository->find($leadId);
        $lead->reject($description);
        $this->leadRepository->save($lead);
    }

}
