<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "quote_segment".
 *
 * @property int $qs_id
 * @property string $qs_departure_time
 * @property string $qs_arrival_time
 * @property int $qs_stop
 * @property string $qs_flight_number
 * @property string $qs_booking_class
 * @property int $qs_duration
 * @property string $qs_departure_airport_code
 * @property string $qs_departure_airport_terminal
 * @property string $qs_arrival_airport_code
 * @property string $qs_arrival_airport_terminal
 * @property string $qs_operating_airline
 * @property string $qs_marketing_airline
 * @property string $qs_air_equip_type
 * @property string $qs_marriage_group
 * @property int $qs_mileage
 * @property string $qs_cabin
 * @property string $qs_meal
 * @property string $qs_fare_code
 * @property int $qs_trip_id
 * @property string $qs_key
 * @property string $qs_created_dt
 * @property string $qs_updated_dt
 * @property int $qs_updated_user_id
 *
 * @property QuoteTrip $trip
 * @property Employee $updatedUser
 * @property QuoteSegmentBaggage[] $quoteSegmentBaggages
 * @property QuoteSegmentBaggageCharge[] $quoteSegmentBaggageCharges
 * @property QuoteSegmentStop[] $quoteSegmentStops
 * @property Airport $arrivalAirport
 * @property Airport $departureAirport
 * @property Airline $marketingAirline
 * @property Airline $operatingAirline
 */
class QuoteSegment extends \yii\db\ActiveRecord
{
    const CABIN_ECONOMY = 'Y', CABIN_PREMIUM_ECONOMY = 'S', CABIN_BUSINESS = 'C',
    CABIN_PREMIUM_BUSINESS = 'J', CABIN_FIRST = 'F', CABIN_PREMIUM_FIRST = 'P';

    public $isOvernight = false;

    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_segment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qs_departure_time', 'qs_arrival_time', 'qs_created_dt', 'qs_updated_dt'], 'safe'],
            [['qs_stop', 'qs_duration', 'qs_mileage', 'qs_trip_id', 'qs_updated_user_id'], 'integer'],
            [['qs_flight_number', 'qs_departure_airport_terminal', 'qs_arrival_airport_terminal'], 'string', 'max' => 5],
            [['qs_booking_class', 'qs_cabin'], 'string', 'max' => 1],
            [['qs_departure_airport_code', 'qs_arrival_airport_code', 'qs_air_equip_type', 'qs_meal'], 'string', 'max' => 3],
            [['qs_marketing_airline', 'qs_marriage_group'], 'string', 'max' => 2],
            [['qs_operating_airline'], 'string', 'max' => 100],
            [['qs_fare_code'], 'string', 'max' => 15],
            [['qs_key'], 'string', 'max' => 255],
            [['qs_arrival_airport_code'], 'exist', 'skipOnError' => true, 'targetClass' => Airport::class, 'targetAttribute' => ['qs_arrival_airport_code' => 'iata']],
            [['qs_departure_airport_code'], 'exist', 'skipOnError' => true, 'targetClass' => Airport::class, 'targetAttribute' => ['qs_departure_airport_code' => 'iata']],
            [['qs_trip_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuoteTrip::class, 'targetAttribute' => ['qs_trip_id' => 'qt_id']],
            [['qs_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['qs_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qs_id' => 'ID',
            'qs_departure_time' => 'Departure Time',
            'qs_arrival_time' => 'Arrival Time',
            'qs_stop' => 'Stop',
            'qs_flight_number' => 'Flight Number',
            'qs_booking_class' => 'Booking Class',
            'qs_duration' => 'Duration',
            'qs_departure_airport_code' => 'Departure Airport Code',
            'qs_departure_airport_terminal' => 'Departure Airport Terminal',
            'qs_arrival_airport_code' => 'Arrival Airport Code',
            'qs_arrival_airport_terminal' => 'Arrival Airport Terminal',
            'qs_operating_airline' => 'Operating Airline',
            'qs_marketing_airline' => 'Marketing Airline',
            'qs_air_equip_type' => 'Air Equip Type',
            'qs_marriage_group' => 'Marriage Group',
            'qs_mileage' => 'Mileage',
            'qs_cabin' => 'Cabin',
            'qs_meal' => 'Meal',
            'qs_fare_code' => 'Fare Code',
            'qs_trip_id' => 'Trip ID',
            'qs_key' => 'Key',
            'qs_created_dt' => 'Created Dt',
            'qs_updated_dt' => 'Updated Dt',
            'qs_updated_user_id' => 'Updated User ID',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketingAirline(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Airline::class, ['iata' => 'qs_marketing_airline']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperatingAirline(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Airline::class, ['iata' => 'qs_operating_airline']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArrivalAirport()
    {
        return $this->hasOne(Airport::class, ['iata' => 'qs_arrival_airport_code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartureAirport()
    {
        return $this->hasOne(Airport::class, ['iata' => 'qs_departure_airport_code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrip()
    {
        return $this->hasOne(QuoteTrip::class, ['qt_id' => 'qs_trip_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'qs_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuoteSegmentBaggages()
    {
        return $this->hasMany(QuoteSegmentBaggage::class, ['qsb_segment_id' => 'qs_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuoteSegmentBaggageCharges()
    {
        return $this->hasMany(QuoteSegmentBaggageCharge::class, ['qsbc_segment_id' => 'qs_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuoteSegmentStops()
    {
        return $this->hasMany(QuoteSegmentStop::class, ['qss_segment_id' => 'qs_id']);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function getIsOVernight()
    {
        $departureTime = new \DateTime($this->qs_departure_time);
        $arrivalTime = new \DateTime($this->qs_arrival_time);

        if(($departureTime->format('H') <= 1 || $departureTime->format('H') >= 22)  && $arrivalTime->format('H') <= 7){
            $this->isOvernight = true;
        }

        return $this->isOvernight;
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->getIsOVernight();
    }

    /**
     * @param null $cabin
     * @return array|mixed|null
     */
    public static function getCabin($cabin = null)
    {
        $mapping = [
            self::CABIN_ECONOMY => 'Economy',
            self::CABIN_PREMIUM_ECONOMY => 'Premium Economy',
            self::CABIN_BUSINESS => 'Business',
            self::CABIN_PREMIUM_BUSINESS => 'Premium Business',
            self::CABIN_FIRST => 'First',
            self::CABIN_PREMIUM_FIRST => 'Premium First',
        ];

        if ($cabin === null) {
            return $mapping;
        }

        return $mapping[$cabin] ?? $cabin;
    }

    /**
     * @param $cabin
     * @return mixed
     */
    public static function getCabinReal($cabin)
    {
        $mapping = [
            'E' =>  self::CABIN_ECONOMY,
            'Economy' =>  self::CABIN_ECONOMY,
            'P' => self::CABIN_PREMIUM_ECONOMY ,
            'Premium eco' => self::CABIN_PREMIUM_ECONOMY ,
            'B'  =>  self::CABIN_BUSINESS,
            'Business'  =>  self::CABIN_BUSINESS,
            'PB'  =>  self::CABIN_PREMIUM_BUSINESS,
            'F'  =>  self::CABIN_FIRST,
            'First'  =>  self::CABIN_FIRST,
            'PF'  =>  self::CABIN_PREMIUM_FIRST,
        ];

        return $mapping[$cabin] ?? $cabin;
    }
}
