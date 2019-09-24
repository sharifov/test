<?php

namespace sales\services\lead;

use common\models\Lead;
use sales\dispatchers\EventDispatcher;
use sales\events\lead\LeadCreatedCloneByUserEvent;
use sales\repositories\lead\LeadRepository;
use sales\repositories\lead\LeadSegmentRepository;
use sales\services\TransactionManager;

/**
 * Class LeadAssignService
 * @property LeadRepository $leadRepository
 * @property LeadSegmentRepository $leadSegmentRepository
 * @property TransactionManager $transactionManager
 * @property EventDispatcher $eventDispatcher
 */
class LeadCloneService
{
    private $leadRepository;
    private $leadSegmentRepository;
    private $transactionManager;
    private $eventDispatcher;

    public function __construct(
        LeadRepository $leadRepository,
        LeadSegmentRepository $leadSegmentRepository,
        TransactionManager $transactionManager,
        EventDispatcher $eventDispatcher
)
    {
        $this->leadRepository = $leadRepository;
        $this->leadSegmentRepository = $leadSegmentRepository;
        $this->transactionManager = $transactionManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param int $leadId
     * @param int $ownerId
     * @param $description
     * @return Lead
     * @throws \Exception
     */
    public function cloneLead(int $leadId, int $ownerId, $description): Lead
    {
        $lead = $this->leadRepository->find($leadId);

        $clone = $this->transactionManager->wrap(function () use ($lead, $ownerId, $description) {

            $clone = $lead->createClone($description);
            $clone->processing($ownerId);

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
