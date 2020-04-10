<?php

namespace sales\forms\lead;

use sales\helpers\lead\LeadFlightSegmentHelper;
use common\components\validators\IataValidator;
use yii\base\Model;

/**
 * Class SegmentForm
 * @property string $origin
 * @property string $originLabel
 * @property string $destination
 * @property string $destinationLabel
 * @property string $departure
 * @property integer $flexibility
 * @property string $flexibilityType
 *
 * @property string $originCity
 * @property string $destinationCity
 */
class SegmentForm extends Model
{

    public $origin;
    public $originLabel;
    public $destination;
    public $destinationLabel;
    public $departure;
    public $flexibility;
    public $flexibilityType;

    public $originCity;
    public $destinationCity;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['origin', 'destination'], 'required'],
            [['origin', 'destination'], IataValidator::class],
            ['destination', 'compare', 'compareAttribute' => 'origin', 'operator' => '!='],
            [['origin', 'destination'], 'filter', 'filter' => function($value) {
                return strtoupper($value);
            }],

            ['departure', 'required'],
            ['departure', 'date', 'format' => 'php:d-M-Y'],
            ['departure', 'filter', 'filter' => function($value) {
                return date('Y-m-d', strtotime($value));
            }],

            ['flexibility', 'integer'],
            ['flexibility', 'in', 'range' => array_keys(LeadFlightSegmentHelper::flexibilityList())],
            ['flexibility', 'filter', 'filter' => function($value) {
                return (int)$value;
            }],

            ['flexibilityType', 'string', 'length' => [1, 3]],
            ['flexibilityType', 'in', 'range' => array_keys(LeadFlightSegmentHelper::flexibilityTypeList())],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'flexibility' => 'Flex (days)',
            'flexibilityType' => 'Flex (+/-)',
        ];
    }
}
