<?php

namespace sales\services\lead;

use common\models\Lead;
use sales\dispatchers\EventDispatcher;
use sales\events\lead\LeadCreatedCloneByUserEvent;
use sales\repositories\lead\LeadRepository;
use sales\repositories\lead\LeadSegmentRepository;
use sales\services\ServiceFinder;
use sales\services\TransactionManager;

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

    public function __construct(
        LeadRepository $leadRepository,
        LeadSegmentRepository $leadSegmentRepository,
        TransactionManager $transactionManager,
        EventDispatcher $eventDispatcher,
        ServiceFinder $serviceFinder
)
    {
        $this->leadRepository = $leadRepository;
        $this->leadSegmentRepository = $leadSegmentRepository;
        $this->transactionManager = $transactionManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->serviceFinder = $serviceFinder;
    }

    /**
     * @param $lead
     * @param int $ownerId
     * @param int|null $creatorId
     * @param string|null $reason
     * @return Lead
     * @throws \Throwable
     */
    public function cloneLead($lead, int $ownerId, ?int $creatorId = null, ?string  $reason = ''): Lead
    {
        $lead = $this->serviceFinder->leadFind($lead);

        $clone = $this->transactionManager->wrap(function () use ($lead, $ownerId, $creatorId, $reason) {

            $clone = $lead->createClone($reason);
            $clone->processing($ownerId, $creatorId, $reason);

            $this->leadRepository->save($clone);

            $this->eventDispatcher->dispatchAll([new LeadCreatedCloneByUserEvent($clone, $ownerId)]);

            foreach ($lead->leadFlightSegments as $segment) {
                $cloneSegment = $segment->createClone($clone->id);
                $this->leadSegmentRepository->save($cloneSegment);
            }

            return $clone;

        });

        return $clone;
    }

}
