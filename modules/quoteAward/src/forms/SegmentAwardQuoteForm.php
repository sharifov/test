<?php

namespace modules\quoteAward\src\forms;

use common\components\validators\IataValidator;
use modules\flight\models\Flight;
use modules\quoteAward\src\models\SegmentAwardQuoteItem;
use yii\base\Model;

class SegmentAwardQuoteForm extends Model
{
    public $origin;
    public $destination;
    public $departure;
    public $arrival;
    public $trip;
    public $flight;
    public $flight_number;
    public $cabin;
    public $operatedBy;
    public $destinationLabel;
    public $originLabel;

    public function __construct(SegmentAwardQuoteItem $segment, $config = [])
    {
        $this->attributes = $segment->attributes;

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['origin', 'destination'], 'required'],
            [['origin', 'destination'], IataValidator::class],
            ['destination', 'compare', 'compareAttribute' => 'origin', 'operator' => '!='],
            [['origin', 'destination'], 'filter', 'filter' => function ($value) {
                return strtoupper($value);
            }],

            ['departure', 'required'],
            ['departure', 'date', 'format' => 'php:Y-m-d H:i'],
            ['departure', 'filter', 'filter' => function ($value) {
                return date('Y-m-d H:i', strtotime($value));
            }],

            ['departure', 'dateValidate'],
            [['destinationLabel', 'originLabel'], 'safe'],

            ['arrival', 'required'],
            ['arrival', 'date', 'format' => 'php:Y-m-d H:i'],
            ['arrival', 'filter', 'filter' => function ($value) {
                return date('Y-m-d H:i', strtotime($value));
            }],

            ['trip', 'required'],

            ['flight', 'required'],
            ['flight_number', 'string'],
            ['operatedBy', 'required'],

            ['cabin', 'string', 'max' => 1],
            ['cabin', 'in', 'range' => array_keys(Flight::getCabinClassList())],
        ];
    }

    public function attributeLabels()
    {
        return [
            'flight_number' => 'Flight No'
        ];
    }

    public function dateValidate($attribute, $params)
    {
        $departure = strtotime($this->departure);
        $arrival = strtotime($this->arrival);
        if ($departure > $arrival) {
            $this->addError($attribute, 'Departure Date can not be less than Arrival Date');
        }
    }
}
