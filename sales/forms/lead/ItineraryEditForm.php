<?php

namespace sales\forms\lead;

use common\models\Lead;
use common\models\LeadFlightSegment;
use sales\forms\CompositeForm;
use sales\helpers\lead\LeadHelper;

/**
 * @property integer $leadId
 * @property string $cabin
 * @property integer $adults
 * @property integer $children
 * @property integer $infants
 * @property SegmentEditForm[] $segmentEditForm
 */
class ItineraryEditForm extends CompositeForm
{
    public $leadId;
    public $cabin;
    public $adults;
    public $children;
    public $infants;

    public $segments;

    public function __construct(Lead $lead, int $countSegmentForms = null, $config = [])
    {
        $this->leadId = $lead->id;
        $this->cabin = $lead->cabin;
        $this->adults = $lead->adults;
        $this->children = $lead->children;
        $this->infants = $lead->infants;

        $this->segmentEditForm = array_map(function ($segment) {
            return new SegmentEditForm($segment);
        }, $this->getSegmentsForms($lead, $countSegmentForms));

        parent::__construct($config);
    }

    public function loadSegmentsForMultiInput(): void
    {
        foreach ($this->segmentEditForm as $segment) {
            $this->segments[] = [
                'origin' => $segment->origin,
                'originLabel' => $segment->originLabel,
                'destination' => $segment->destination,
                'destinationLabel' => $segment->destinationLabel,
                'departure' => $segment->departure,
                'flexibility' => $segment->flexibility,
                'flexibilityType' => $segment->flexibilityType
            ];
        }
    }

    public function rules(): array
    {
        return [

            ['leadId', 'integer'],
            ['leadId', 'exist', 'targetClass' => Lead::class, 'targetAttribute' => ['leadId' => 'id']],

            ['cabin', 'string', 'max' => 1],
            ['cabin', 'in', 'range' => array_keys(LeadHelper::cabinList())],

            [['adults', 'children', 'infants'], 'integer', 'min' => 0, 'max' => 9],

            ['infants', function () {
                if ($this->infants > $this->adults) {
                    $this->addError('infants', 'Infants must be no greater than Adults');
                }
            }],

            ['segments', 'safe']

        ];
    }

    private function getSegmentsForms(Lead $lead, int $countSegmentForms = null)
    {
        $countRelations = $lead->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->count();
        if ($countSegmentForms === null || ($countSegmentForms === $countRelations)) {
            return $lead->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->all();
        } elseif ($countSegmentForms > $countRelations) {
            $segmentForms = $lead->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->all();
            for ($i = 0; $i < ($countSegmentForms - $countRelations); $i++) {
                $segmentForms[] = new LeadFlightSegment();
            }
            return $segmentForms;
        } else {
            return $lead->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->limit($countSegmentForms)->all();
        }
    }

    public function isEmpty(): bool
    {
        return $this->leadId ? false : true;
    }

    public function internalForms(): array
    {
        return ['segmentEditForm'];
    }
}
