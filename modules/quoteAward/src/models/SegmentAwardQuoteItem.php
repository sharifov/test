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
                'arrival', 'trip', 'flight', 'cabin', 'operatedBy'], 'safe'],
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
}
