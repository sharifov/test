<?php

namespace sales\services\lead;

use common\models\Lead;
use sales\repositories\lead\LeadRepository;
use sales\repositories\lead\LeadSegmentRepository;
use sales\services\TransactionManager;

/**
 * Class LeadAssignService
 * @property LeadRepository $leadRepository
 * @property LeadSegmentRepository $leadSegmentRepository
 * @property TransactionManager $transactionManager
 */
class LeadCloneService
{
    private $leadRepository;
    private $leadSegmentRepository;
    private $transactionManager;

    public function __construct(
        LeadRepository $leadRepository,
        LeadSegmentRepository $leadSegmentRepository,
        TransactionManager $transactionManager)
    {
        $this->leadRepository = $leadRepository;
        $this->leadSegmentRepository = $leadSegmentRepository;
        $this->transactionManager = $transactionManager;
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

            $clone = $lead->createClone($ownerId, $description);

            $this->leadRepository->save($clone);

            foreach ($lead->leadFlightSegments as $segment) {
                $cloneSegment = $segment->createClone($clone->id);
                $this->leadSegmentRepository->save($cloneSegment);
            }

            return $clone;

        });

        return $clone;
    }

}
