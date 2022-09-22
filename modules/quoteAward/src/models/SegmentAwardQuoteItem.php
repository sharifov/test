<?php

namespace modules\quoteAward\src\models;

use common\models\Lead;
use yii\base\Model;

class SegmentAwardQuoteItem extends Model
{
    public const TRIP_COUNT = 10;

    public $origin;
    public $destination;
    public $departure;
    public $arrival;
    public $trip;
    public $flight;
    public $flight_number;
    public $cabin;
    public $operatedBy;

    public $originName;
    public $destinationName;

    public function rules(): array
    {
        return [
            [['origin', 'destination', 'departure',
                'arrival', 'trip', 'flight', 'flight_number', 'cabin', 'operatedBy'], 'safe'],
        ];
    }


    public function __construct(?Lead $lead = null, int $tripId = 1, $config = [])
    {
        $this->trip = $tripId;
        $this->flight = 0;

        if ($lead) {
            $this->cabin = $lead->cabin;
        }
        parent::__construct($config);
    }

    public function setParams($params)
    {
        $this->setAttributes($params);
    }

    public static function getTrips(): array
    {
        $trips = [];
        for ($i = 1; $i <= self::TRIP_COUNT; $i++) {
            $trips[$i] = 'Trip ' . $i;
        }
        return $trips;
    }

    public function loadData(array $segment, int $trip)
    {
        $this->origin = $segment['departureAirport'] ?? null;
        $this->destination = $segment['arrivalAirport'] ?? null;

        if (array_key_exists('departureDateTime', $segment) && $segment['departureDateTime'] instanceof \DateTime) {
            $this->departure = $segment['departureDateTime']->format('Y-m-d H:i');
        }

        if (array_key_exists('arrivalDateTime', $segment) && $segment['arrivalDateTime'] instanceof \DateTime) {
            $this->arrival = $segment['arrivalDateTime']->format('Y-m-d H:i');
        }
        $this->trip = $trip;
        $this->flight = 0;
        $this->flight_number = $segment['flightNumber'] ?? null;
        $this->cabin = $segment['cabin'] ?? null;
        $this->operatedBy = $segment['carrier'] ?? null;
    }
}
