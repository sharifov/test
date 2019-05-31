<?php

namespace sales\forms\lead;

use common\models\LeadFlightSegment;
use yii\base\Model;

class SegmentForm extends Model
{

    public $segmentId;
    public $origin;
    public $destination;
    public $originLabel;
    public $destinationLabel;
    public $departure;
    public $flexibility;
    public $flexibilityType;

    public function __construct(LeadFlightSegment $flightSegment = null, $config = [])
    {
        if ($flightSegment) {
            $this->segmentId = $flightSegment->id;
            $this->origin = $flightSegment->origin;
            $this->destination = $flightSegment->destination;
            $this->originLabel = $flightSegment->origin_label;
            $this->destinationLabel = $flightSegment->destination_label;
            $this->departure = $flightSegment->departure;
            $this->flexibility = $flightSegment->flexibility;
            $this->flexibilityType = $flightSegment->flexibility_type;
        }

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['origin'], 'integer'],
            [['origin'], 'required'],
            [['destination'], 'required'],
            [['destination'], 'integer'],
            [['originLabel'], 'required'],
            [['destinationLabel'], 'required'],
            [['departure'], 'required'],
            [['flexibility'], 'required'],
            [['flexibilityType'], 'required'],
        ];
    }
}
