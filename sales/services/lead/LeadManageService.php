<?php

namespace sales\services\lead;

use common\models\LeadFlightSegment;
use sales\forms\lead\ItineraryEditForm;
use sales\forms\lead\SegmentEditForm;
use sales\repositories\lead\LeadRepository;
use sales\repositories\lead\LeadSegmentRepository;
use sales\services\TransactionManager;
use yii\helpers\VarDumper;

class LeadManageService
{
    private $leads;
    private $segments;
    private $transaction;

    public function __construct(LeadRepository $leads, LeadSegmentRepository $segments, TransactionManager $transaction)
    {
        $this->leads = $leads;
        $this->segments = $segments;
        $this->transaction = $transaction;
    }

    public function editItinerary($id, ItineraryEditForm $form): void
    {
        $lead = $this->leads->get($id);
        $lead->editItinerary(
            $form->cabin,
            $form->adults,
            $form->children,
            $form->infants
        );
        $this->transaction->wrap(function () use ($lead, $form) {
            $newIds = [];
            /** @var SegmentEditForm $segmentForm */
            foreach ($form->segmentEditForm as $segmentForm) {
                if ($segmentForm->segmentId) {
                    /** @var LeadFlightSegment $segment */
                    $segment = $this->segments->get($segmentForm->segmentId);
                    $segment->edit(
                        $segmentForm->origin,
                        $segmentForm->destination,
                        $segmentForm->departure,
                        $segmentForm->flexibility,
                        $segmentForm->flexibilityType
                    );
                } else {
                    $segment = LeadFlightSegment::create(
                        $lead->id,
                        $segmentForm->origin,
                        $segmentForm->destination,
                        $segmentForm->departure,
                        $segmentForm->flexibility,
                        $segmentForm->flexibilityType
                    );
                }
                $this->segments->save($segment);
                $newIds[] = $segment->id;
            }
            $this->leads->removeOldSegments($lead, $newIds);
            $this->leads->save($lead);
        });
    }
}
