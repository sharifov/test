<?php

namespace src\services\lead;

use common\models\Employee;
use common\models\Lead;
use modules\eventManager\src\EventApp;
use modules\lead\src\events\LeadEvents;
use src\access\EmployeeAccess;
use src\model\leadStatusReason\HandleReasonDto;
use src\model\leadStatusReason\LeadStatusReasonService;
use src\repositories\lead\LeadRepository;
use src\services\ServiceFinder;
use src\services\TransactionManager;
use yii\base\Event;

/**
 * Class LeadStateService
 *
 * @property ServiceFinder $serviceFinder
 * @property LeadRepository $leadRepository
 * @property LeadStatusReasonService $leadStatusReasonService
 * @property TransactionManager $transactionManager
 */
class LeadStateService
{
    private $serviceFinder;
    private $leadRepository;
    private $leadStatusReasonService;
    private $transactionManager;

    /**
     * @param ServiceFinder $serviceFinder
     * @param LeadRepository $leadRepository
     * @param LeadStatusReasonService $leadStatusReasonService
     * @param TransactionManager $transactionManager
     */
    public function __construct(
        ServiceFinder $serviceFinder,
        LeadRepository $leadRepository,
        LeadStatusReasonService $leadStatusReasonService,
        TransactionManager $transactionManager
    ) {
        $this->serviceFinder = $serviceFinder;
        $this->leadRepository = $leadRepository;
        $this->leadStatusReasonService = $leadStatusReasonService;
        $this->transactionManager = $transactionManager;
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
     * @param int|null $originId
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function duplicate($lead, $newOwner, ?int $originId, ?int $creatorId = null, ?string $reason = ''): void
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
     * @param int|Lead $lead
     * @param int|Employee|null $newOwner
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function new($lead, $newOwner, ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $newOwnerId = $this->newOwnerFind($newOwner, $lead);
        $lead->new($newOwnerId, $creatorId, $reason);
        $this->leadRepository->save($lead);
    }

    public function extraQueue($lead, $newOwner, ?int $creatorId = null, ?string $reason = null): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $newOwnerId = $this->newOwnerFind($newOwner, $lead);
        $lead->extraQueue($newOwnerId, $creatorId, $reason);
        $this->leadRepository->save($lead);
    }

    public function close($lead, ?string $leadStatusReasonKey = null, ?int $creatorId = null, ?string $reasonComment = '', ?int $originId = null): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $lead->close($leadStatusReasonKey, $creatorId, $reasonComment);
        $dto = new HandleReasonDto(
            $lead,
            $leadStatusReasonKey,
            null,
            $creatorId,
            $reasonComment,
            $originId
        );
        Event::on(
            LeadEvents::class,
            LeadEvents::EVENT_CLOSE,
            [EventApp::class, EventApp::HANDLER],
            ['dto' => $dto]
        );
        $this->transactionManager->wrap(function () use ($dto) {
            $this->leadRepository->save($dto->lead);
            Event::trigger(
                LeadEvents::class,
                LeadEvents::EVENT_CLOSE
            );
        });
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
