<?php

namespace sales\forms\lead;

use common\models\Lead;
use sales\forms\CompositeForm;
use sales\helpers\lead\LeadHelper;

/**
 * @property SegmentForm[] $segmentform
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
            $this->segmentform = array_map(function ($segment) {
                return new SegmentForm($segment);
            }, $lead->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->all());
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [

            [['leadId'], 'integer'],

            ['cabin', 'string', 'max' => 1],
            ['cabin', 'in', 'range' => array_keys(LeadHelper::cabinList())],

            [['adults', 'children', 'infants'], 'integer', 'min' => 0, 'max' => 9],

            ['infants', function() {
                if ($this->infants > $this->adults) {
                    $this->addError('infants', 'Infants must be no greater than Adults');
                }
            }],

        ];
    }

    public function isEmpty() : bool
    {
        return $this->leadId ? false : true;
    }

    public function internalForms(): array
    {
        return ['segmentform'];
    }
}
