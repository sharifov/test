<?php

namespace sales\forms\lead;

use common\models\Lead;
use sales\forms\CompositeForm;

/**
 * @property SegmentForm[] $segments
 */

class ItineraryForm extends CompositeForm
{
    public $leadId;
    public $cabin;
    public $adults;
    public $children;
    public $infants;

    public function __construct(Lead $lead = null, $config = [])
    {
        if ($lead) {

            $this->leadId = $lead->id;
            $this->cabin = $lead->cabin;
            $this->adults = $lead->adults;
            $this->children = $lead->children;
            $this->infants = $lead->infants;

//            if ($leadFlightSegments = $lead->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->all()) {
//                foreach ($leadFlightSegments as $leadFlightSegment) {
//                    $this->segment = new SegmentForm($leadFlightSegment);
//                }
//            }

//            $this->segments = array_map(function ($segment) {
//                return new SegmentForm($segment);
//            }, $lead->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->all());

//            if ($leadFlightSegments = $lead->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->all()) {
//                foreach ($leadFlightSegments as $leadFlightSegment) {
//                    $this->segment = new SegmentForm($leadFlightSegment);
//                }
//            }

        }

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['leadId'], 'required'],
            [['cabin'], 'required'],
            [['adults'], 'integer'],
            [['children'], 'integer'],
            [['infants'], 'integer'],
        ];
    }

    public function isEmpty() : bool
    {
        return $this->leadId ? false : true;
    }

    public function internalForms(): array
    {
        return ['segments'];
    }
}
