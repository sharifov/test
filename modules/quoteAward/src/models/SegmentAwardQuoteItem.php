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

    public function rules(): array
    {
        return [
            [['origin', 'destination', 'departure',
                'arrival', 'trip', 'flight'], 'safe'],
        ];
    }


    public function __construct(?Lead $lead = null, $config = [])
    {
        $this->trip = 1;
        $this->flight = 1;
        parent::__construct($config);
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
