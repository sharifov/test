<?php

namespace sales\forms\lead;

use common\models\LeadFlightSegment;
use sales\helpers\lead\LeadFlightSegmentHelper;
use sales\repositories\airport\AirportRepository;
use sales\validators\DateValidator;
use sales\validators\IataValidator;
use Yii;
use yii\base\Model;

/**
 * Class SegmentEditForm
 * @property integer $segmentId
 * @property string $origin
 * @property string $destination
 * @property string $originLabel
 * @property string $destinationLabel
 * @property string $departure
 * @property integer $flexibility
 * @property string $flexibilityType
 */
class SegmentEditForm extends Model
{
    public $segmentId;
    public $origin;
    public $destination;
    public $originLabel;
    public $destinationLabel;
    public $departure;
    public $flexibility;
    public $flexibilityType;

    public function __construct(LeadFlightSegment $segment, $config = [])
    {
        if (!$segment->getIsNewRecord()) {
            $this->segmentId = $segment->id;
            $this->origin = $segment->origin;
            $this->originLabel = $this->loadAirportLabel($this->origin);
            $this->destination = $segment->destination;
            $this->destinationLabel = $this->loadAirportLabel($this->destination);
            $this->departure = $segment->departure;
            $this->flexibility = $segment->flexibility;
            $this->flexibilityType = $segment->flexibility_type;
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['origin', 'destination'], 'required'],
            [['origin', 'destination'], IataValidator::class],

            ['departure', 'required'],
            ['departure', DateValidator::class, 'format' => 'd-M-Y'],

            ['flexibility', 'integer'],
            ['flexibility', 'in', 'range' => array_keys(LeadFlightSegmentHelper::flexibilityList())],

            ['flexibilityType', 'string', 'length' => [1, 3]],
            ['flexibilityType', 'in', 'range' => array_keys(LeadFlightSegmentHelper::flexibilityTypeList())],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'flexibility' => 'Flex (days)',
            'flexibilityType' => 'Flex (+/-)',
        ];
    }

    private function loadAirportLabel($iata): string
    {
        try {
            return (new AirportRepository())->getByIata($iata)->getSelection();
        } catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            return '';
        }
    }
}
