<?php

namespace src\services\lead;

use common\models\Lead;
use src\dispatchers\EventDispatcher;
use src\events\lead\LeadCreatedCloneByUserEvent;
use src\model\leadData\services\LeadDataCloneService;
use src\repositories\lead\LeadRepository;
use src\repositories\lead\LeadSegmentRepository;
use src\services\ServiceFinder;
use src\services\TransactionManager;
use src\services\lead\LeadPreferencesCloneService as LPCloneService;


/**
 * Class LeadAssignService
 * @property LeadRepository $leadRepository
 * @property LeadSegmentRepository $leadSegmentRepository
 * @property TransactionManager $transactionManager
 * @property EventDispatcher $eventDispatcher
 * @property ServiceFinder $serviceFinder
 */
class LeadCloneService
{
    private $leadRepository;
    private $leadSegmentRepository;
    private $transactionManager;
    private $eventDispatcher;
    private $serviceFinder;
    private $leadPreferencesCloneService;


    public function __construct(
        LeadRepository $leadRepository,
        LeadSegmentRepository $leadSegmentRepository,
        TransactionManager $transactionManager,
        EventDispatcher $eventDispatcher,
        ServiceFinder $serviceFinder,
        LPCloneService $leadPreferencesCloneService

    ) {
        $this->leadRepository = $leadRepository;
        $this->leadSegmentRepository = $leadSegmentRepository;
        $this->transactionManager = $transactionManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->serviceFinder = $serviceFinder;
        $this->leadPreferencesCloneService = $leadPreferencesCloneService;
    }

    /**
     * @param $lead
     * @param int $ownerId
     * @param int|null $creatorId
     * @param string|null $reason
     * @return Lead
     * @throws \Throwable
     */
    public function cloneLead($lead, int $ownerId, ?int $creatorId = null, ?string $reason = ''): Lead
    {
        $lead = $this->serviceFinder->leadFind($lead);

        $clone = $this->transactionManager->wrap(function () use ($lead, $ownerId, $creatorId, $reason) {

            $ownerOfOriginalLead = $lead->employee_id;

            $clone = $lead->createClone($reason);
            $clone->processing($ownerId, $creatorId, $reason);

            $this->leadRepository->save($clone);

            $this->leadPreferencesCloneService->cloneLeadPreferences($lead->id, $clone->id);
            $this->eventDispatcher->dispatchAll([new LeadCreatedCloneByUserEvent($clone, $ownerId, $ownerOfOriginalLead)]);

            foreach ($lead->leadFlightSegments as $segment) {
                $cloneSegment = $segment->createClone($clone->id);
                $this->leadSegmentRepository->save($cloneSegment);
            }

            LeadDataCloneService::cloneByLead($lead, $clone->id);

            return $clone;
        });

        return $clone;
    }
}
