<?php

namespace modules\flight\src\useCases\reprotectionCreate\form\flightQuote\tripsForm\segmentsForm;

use yii\base\Model;

/**
 * Class ReProtectionFlightQuoteForm
 *
 * @property $departureTime
 * @property $arrivalTime
 * @property $flightNumber
 * @property $bookingClass
 * @property $stop
 * @property $stops
 * @property $duration
 * @property $departureAirportCode
 * @property $departureAirportTerminal
 * @property $arrivalAirportCode
 * @property $arrivalAirportTerminal
 * @property $operatingAirline
 * @property $airEquipType
 * @property $marketingAirline
 * @property $marriageGroup
 * @property $mileage
 * @property $meal
 * @property $fareCode
 * @property $baggage
 * @property $brandId
 */
class SegmentForm extends Model
{
    public $departureTime;
    public $arrivalTime;
    public $flightNumber;
    public $bookingClass;
    public $stop;
    public $stops;
    public $duration;
    public $departureAirportCode;
    public $departureAirportTerminal;
    public $arrivalAirportCode;
    public $arrivalAirportTerminal;
    public $operatingAirline;
    public $airEquipType;
    public $marketingAirline;
    public $marriageGroup;
    public $mileage;
    public $meal;
    public $fareCode;
    public $baggage;
    public $brandId;

    public function rules(): array
    {
        return [
            [['departureTime', 'arrivalTime', 'departureAirportCode', 'arrivalAirportCode'], 'required'],

            //[['departureTime', 'arrivalTime'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['departureTime', 'arrivalTime'], 'safe'],

            [['departureAirportCode', 'arrivalAirportCode'], 'string', 'max' => 3],

            ['flightNumber', 'integer'],

            ['bookingClass', 'string', 'max' => 1],

            ['stop', 'safe'],
            ['stops', 'safe'], /* TODO::  */

            ['duration', 'integer'],

            [['departureAirportTerminal', 'arrivalAirportTerminal'], 'string', 'max' => 3],

            [['operatingAirline', 'marketingAirline'], 'string', 'max' => 2],

            ['airEquipType', 'string', 'max' => 30],

            ['marriageGroup', 'string', 'max' => 3],

            ['mileage', 'integer'],

            ['meal', 'string', 'max' => 2],

            ['fareCode', 'string', 'max' => 50],

            ['baggage', 'safe'], /* TODO::  */

            ['brandId', 'safe'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
