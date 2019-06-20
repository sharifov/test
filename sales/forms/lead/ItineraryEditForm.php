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
 * @property string $tripType
 * @property string $mode
 *
 * @property SegmentEditForm[] $segments
 */
class ItineraryEditForm extends CompositeForm
{
    public const MODE_VIEW = 'view';
    public const MODE_EDIT = 'edit';

    public $leadId;
    public $cabin;
    public $adults;
    public $children;
    public $infants;
    public $tripType;

    public $mode = self::MODE_VIEW;

    /**
     * ItineraryEditForm constructor.
     * @param Lead $lead
     * @param int|null $countSegmentForms
     * @param array $config
     */
    public function __construct(Lead $lead, int $countSegmentForms = null, $config = [])
    {

        $this->leadId = $lead->id;
        $this->cabin = $lead->cabin;
        $this->adults = $lead->adults;
        $this->children = $lead->children;
        $this->infants = $lead->infants;
        $this->tripType = $lead->trip_type;

        $this->segments = array_map(function ($segment) {
            return new SegmentEditForm($segment);
        }, $this->getSegmentsForms($lead, $countSegmentForms));

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [

            ['leadId', 'required'],
            ['leadId', 'integer'],
            ['leadId', 'exist', 'targetClass' => Lead::class, 'targetAttribute' => ['leadId' => 'id']],

            ['cabin', 'string', 'max' => 1],
            ['cabin', 'in', 'range' => array_keys(LeadHelper::cabinList())],

            [['adults', 'children', 'infants'], 'integer', 'min' => 0, 'max' => 9],
            [['adults', 'children', 'infants'], 'in', 'range' => array_keys(LeadHelper::AdultsChildrenInfantsList())],

            ['adults', function () {
                if (!$this->adults && !$this->children) {
                    $this->addError('adults', 'Adults or Children must be more 0.');
                }
            }],

            ['infants', function () {
                if ($this->infants > $this->adults) {
                    $this->addError('infants', 'Infants must be no greater than Adults');
                }
            }],

            ['segments', function () {
                if ( !is_array($this->segments)) {
                    return;
                }
                foreach ($this->segments as $key => $segment) {
                    if (isset($this->segments[$key - 1])) {
                        $dateFrom = strtotime($this->segments[$key - 1]->departure);
                        $dateTo = strtotime($this->segments[$key]->departure);
                        if ($dateTo < $dateFrom) {
                            $this->addError('segments[' . $key . '][departure]', 'Date can not be less than the date of the previous segment');
                        }
                    }
                }
            }]

        ];
    }

    /**
     * @return array
     */
    public function internalForms(): array
    {
        return ['segments'];
    }


    public function setViewMode(): void
    {
        $this->mode = self::MODE_VIEW;
    }

    public function setEditMode(): void
    {
        $this->mode = self::MODE_EDIT;
    }

    /**
     * @return bool
     */
    public function isViewMode(): bool
    {
        return $this->mode === self::MODE_VIEW;
    }

    /**
     * @return bool
     */
    public function isEditMode(): bool
    {
        return $this->mode === self::MODE_EDIT;
    }

    /**
     * @param Lead $lead
     * @param int|null $countSegmentForms
     * @return array
     */
    private function getSegmentsForms(Lead $lead, int $countSegmentForms = null): array
    {
        $countRelations = $lead->getleadFlightSegments()->count();

        if ($countSegmentForms === null || ($countSegmentForms === $countRelations)) {
            return $lead->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->all();
        }
        if ($countSegmentForms > $countRelations) {
            $segmentForms = $lead->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->all();
            for ($i = 0; $i < ($countSegmentForms - $countRelations); $i++) {
                $segmentForms[] = new LeadFlightSegment();
            }
            return $segmentForms;
        }
        return $lead->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->limit($countSegmentForms)->all();
    }
}
