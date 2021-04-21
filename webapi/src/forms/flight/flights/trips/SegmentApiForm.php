<?php

namespace  webapi\src\forms\flight\flights\trips;

use yii\base\Model;

/**
 * Class SegmentApiForm
 *
 * @property $segmentId
 * @property $airline
 * @property $airlineName
 * @property $mainAirline
 * @property $arrivalAirport
 * @property $arrivalTime
 * @property $departureAirport
 * @property $departureTime
 * @property $bookingClass
 * @property $flightNumber
 * @property $statusCode
 * @property $operatingAirline
 * @property $operatingAirlineCode
 * @property $cabin
 * @property $departureCity
 * @property $arrivalCity
 * @property $departureCountry
 * @property $arrivalCountry
 * @property $departureAirportName
 * @property $arrivalAirportName
 * @property $flightDuration
 * @property $layoverDuration
 * @property $airlineRecordLocator
 * @property $aircraft
 * @property $baggage
 * @property $carryOn
 * @property $marriageGroup
 * @property $fareCode
 * @property $mileage
 *
 * @property $tripKey
 */
class SegmentApiForm extends Model
{
    public $segmentId;
    public $airline;
    public $airlineName;
    public $mainAirline;
    public $arrivalAirport;
    public $arrivalTime;
    public $departureAirport;
    public $departureTime;
    public $bookingClass;
    public $flightNumber;
    public $operatingAirline;
    public $operatingAirlineCode;
    public $cabin;
    public $departureCity;
    public $arrivalCity;
    public $departureCountry;
    public $arrivalCountry;
    public $departureAirportName;
    public $arrivalAirportName;
    public $flightDuration;
    public $layoverDuration;
    public $airlineRecordLocator;
    public $aircraft;
    public $baggage;
    public $carryOn;
    public $marriageGroup;
    public $fareCode;
    public $mileage;
    public $statusCode;

    private int $tripKey;

    public function __construct(int $tripKey, $config = [])
    {
        $this->tripKey = $tripKey;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['segmentId'], 'integer'],
            [['airline'], 'string', 'max' => 2],
            [['airlineName'], 'string'],
            [['mainAirline'], 'string', 'max' => 2],
            [['arrivalAirport'], 'string', 'max' => 3],
            [['arrivalTime'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['departureAirport'], 'string', 'max' => 3],
            [['departureTime'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['bookingClass'], 'string'],
            [['flightNumber'], 'integer'],
            [['statusCode'], 'string'],
            [['operatingAirline'], 'string'],
            [['operatingAirlineCode'], 'string', 'max' => 2],
            [['cabin'], 'string'],
            [['departureCity'], 'string'],
            [['arrivalCity'], 'string'],
            [['departureCountry'], 'string', 'max' => 2],
            [['arrivalCountry'], 'string', 'max' => 2],
            [['departureAirportName'], 'string'],
            [['arrivalAirportName'], 'string'],
            [['flightDuration'], 'integer'],
            [['layoverDuration'], 'integer'],
            [['airlineRecordLocator'], 'string'],
            [['baggage'], 'integer'],
            [['carryOn'], 'boolean'],

            [['aircraft'], 'safe'],
            [['marriageGroup'], 'safe'],
            [['fareCode'], 'safe'],
            [['mileage'], 'safe'],
        ];
    }

    public function getTripKey(): int
    {
        return $this->tripKey;
    }

    public function formName(): string
    {
        return '';
    }
}
