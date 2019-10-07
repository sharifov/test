<?php

namespace sales\services\lead;

use common\models\Employee;
use common\models\Lead;
use sales\access\EmployeeAccess;
use sales\repositories\lead\LeadRepository;
use sales\services\ServiceFinder;
use yii\helpers\VarDumper;

/**
 * Class LeadStateService
 *
 * @property ServiceFinder $serviceFinder
 * @property LeadRepository $leadRepository
 */
class LeadStateService
{

    private $serviceFinder;
    private $leadRepository;

    /**
     * @param ServiceFinder $serviceFinder
     * @param LeadRepository $leadRepository
     */
    public function __construct(
        ServiceFinder $serviceFinder,
        LeadRepository $leadRepository
    )
    {
        $this->serviceFinder = $serviceFinder;
        $this->leadRepository = $leadRepository;
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee|null $newOwner
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function processing($lead, $newOwner, ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $newOwnerId = $this->newOwnerFind($newOwner, $lead);

        if ($lead->isCompleted()) {
            throw new \DomainException('Lead is completed!');
        }

        if (!$lead->isAvailableToProcessing()) {
            throw new \DomainException('Lead is unavailable to "Processing" now!');
        }

        $lead->processing($newOwnerId, $creatorId, $reason);
        $this->leadRepository->save($lead);
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee|null $newOwner
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function booked($lead, $newOwner, ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $newOwnerId = $this->newOwnerFind($newOwner, $lead);
        $lead->booked($newOwnerId, $creatorId, $reason);
        $this->leadRepository->save($lead);
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee|null $newOwner
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function sold($lead, $newOwner, ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $newOwnerId = $this->newOwnerFind($newOwner, $lead);
        $lead->sold($newOwnerId, $creatorId, $reason);
        $this->leadRepository->save($lead);
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee|null $newOwner
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function pending($lead, $newOwner, ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $newOwnerId = $this->newOwnerFind($newOwner, $lead);
        $lead->pending($newOwnerId, $creatorId, $reason);
        $this->leadRepository->save($lead);
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee|null $newOwner
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function followUp($lead, $newOwner, ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $newOwnerId = $this->newOwnerFind($newOwner, $lead);
        $lead->followUp($newOwnerId, $creatorId, $reason);
        $this->leadRepository->save($lead);
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee|null $newOwner
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function trash($lead, $newOwner, ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $newOwnerId = $this->newOwnerFind($newOwner, $lead);
        $lead->trash($newOwnerId, $creatorId, $reason);
        $this->leadRepository->save($lead);
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee|null $newOwner
     * @param int $originId
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function duplicate($lead, $newOwner, int $originId, ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $newOwnerId = $this->newOwnerFind($newOwner, $lead);
        $lead->duplicate($originId, $newOwnerId, $creatorId, $reason);
        $this->leadRepository->save($lead);
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee|null $newOwner
     * @param string|null $snoozeFor
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function snooze($lead, $newOwner, ?string $snoozeFor = '', ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $newOwnerId = $this->newOwnerFind($newOwner, $lead);
        $lead->snooze($snoozeFor, $newOwnerId, $creatorId, $reason);
        $this->leadRepository->save($lead);
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee|null $newOwner
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function reject($lead, $newOwner, ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $newOwnerId = $this->newOwnerFind($newOwner, $lead);
        $lead->reject($newOwnerId, $creatorId, $reason);
        $this->leadRepository->save($lead);
    }

    /**
     * @param $newOwner
     * @param Lead $lead
     * @return int|null
     */
    private function newOwnerFind($newOwner, Lead $lead): ?int
    {
        if ($newOwner !== null) {
            $owner = $this->serviceFinder->userFind($newOwner);
            EmployeeAccess::leadAccess($lead, $owner);
            return $owner->id;
        }
        return $newOwner;
    }

}
