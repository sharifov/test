<?php

namespace common\models\local;

use Yii;
use yii\base\Model;

class FlightSegment extends Model
{
    public $airlineCode;
    public $departureAirportCode;
    public $destinationAirportCode;
    public $departureTime;
    public $arrivalTime;
    public $flightNumber;
    public $mainAirlineCode;
    public $duration;
    public $bookingClass;
    public $cabin;
    public $operationAirlineCode;
    public $aircraftCode;
    public $aircraftModel;
    public $airlineRecordLocator;
    public $baggageAllowanceNumber;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'airlineCode' => 'Airline Code',
            'departureAirportCode' => 'Departure Airport Code',
            'destinationAirportCode' => 'Destination Airport Code',
            'departureTime' => 'Departure Time',
            'arrivalTime' => 'Arrival Time',
            'flightNumber' => 'Flight Number',
            'mainAirlineCode' => 'Main Airline Code',
            'duration' => 'Duration',
            'bookingClass' => 'Booking Class',
            'aircraftModel' => 'Aircraft Model',
            'cabin' => 'Cabin',
            'operationAirlineCode' => 'Operation Airline Code',
            'aircraftCode' => 'Aircraft Code',
            'airlineRecordLocator' => 'Airline Record Locator',
            'baggageAllowanceNumber' => 'Baggage Allowance Number',
        ];
    }
}
