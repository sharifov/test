<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

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
 * @property bool $qs_cabin_basic [tinyint(1)]
 * @property string $qs_meal
 * @property string $qs_fare_code
 * @property int $qs_trip_id
 * @property string $qs_key
 * @property string $qs_created_dt
 * @property string $qs_updated_dt
 * @property int $qs_updated_user_id
 * @property int $qs_ticket_id
 * @property boolean $qs_recheck_baggage
 *
 * @property QuoteTrip $trip
 * @property Employee $updatedUser
 * @property QuoteSegmentBaggage[] $quoteSegmentBaggages
 * @property QuoteSegmentBaggageCharge[] $quoteSegmentBaggageCharges
 * @property QuoteSegmentStop[] $quoteSegmentStops
 * @property Airports $arrivalAirport
 * @property Airports $departureAirport
 * @property Airline $marketingAirline
 * @property-read string $ticketColor
 * @property-read bool $isOVernight
 * @property Airline $operatingAirline
 */
class QuoteSegment extends \yii\db\ActiveRecord
{
    public const SCENARIO_MANUALLY = 'manually';
    public const SCENARIO_CRUD = 'crud';

    public const CABIN_ECONOMY              = 'Y';
    public const CABIN_PREMIUM_ECONOMY      = 'S';
    public const CABIN_BUSINESS             = 'C';
    public const CABIN_PREMIUM_BUSINESS     = 'J';
    public const CABIN_FIRST                = 'F';
    public const CABIN_PREMIUM_FIRST        = 'P';

    public $isOvernight = false;

    public const TICKET_COLOR_LIST = [
        0   => '#FFFFFF',
        1   => '#fbe5e1',
        2   => '#fafbe1',
        3   => '#e1fbec',
    ];

    /**
     * @param array $attributes
     * @param int $qtId
     * @return QuoteSegment
     */
    public static function clone(array $attributes, int $qtId): self
    {
        $segment = new self();
        $segment->attributes = $attributes;
        $segment->qs_trip_id = $qtId;
        return $segment;
    }

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
            [['qs_departure_time', 'qs_arrival_time', 'qs_trip_id', 'qs_departure_airport_code', 'qs_arrival_airport_code', 'qs_duration'], 'required', 'on' => [self::SCENARIO_CRUD]],
            [['qs_stop', 'qs_duration', 'qs_mileage', 'qs_trip_id', 'qs_updated_user_id', 'qs_ticket_id', 'qs_cabin_basic'], 'integer'],
            [['qs_flight_number', 'qs_departure_airport_terminal', 'qs_arrival_airport_terminal'], 'string', 'max' => 5],
            [['qs_booking_class', 'qs_cabin'], 'string', 'max' => 1],
            [['qs_departure_airport_code', 'qs_arrival_airport_code', 'qs_meal'], 'string', 'max' => 3],
            [['qs_marketing_airline', 'qs_marriage_group'], 'string', 'max' => 2],
            [['qs_operating_airline'], 'string', 'max' => 100],
            [['qs_fare_code'], 'string', 'max' => 50],
            [['qs_air_equip_type'], 'string', 'max' => 30],
            [['qs_key'], 'string', 'max' => 255],
            [['qs_trip_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuoteTrip::class, 'targetAttribute' => ['qs_trip_id' => 'qt_id']],
            [['qs_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['qs_updated_user_id' => 'id']],
            [['qs_recheck_baggage'], 'boolean'],
            [['qs_duration'], 'integer', 'min' => 0, 'on' => [self::SCENARIO_MANUALLY, self::SCENARIO_CRUD]],
            [['qs_arrival_airport_code'],
                'exist', 'skipOnError' => true, 'targetClass' => Airports::class,
                'targetAttribute' => ['qs_arrival_airport_code' => 'iata'],
                'message' => 'Arrival Airport Code(' . $this->qs_arrival_airport_code . ') not found in Airports'],
            [['qs_departure_airport_code'], 'exist', 'skipOnError' => true, 'targetClass' => Airports::class,
                'targetAttribute' => ['qs_departure_airport_code' => 'iata'],
                'message' => 'Departure Airport Code(' . $this->qs_departure_airport_code . ') not found in Airports'],
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
            'qs_ticket_id' => 'Ticket Id',
             'qs_recheck_baggage' => 'Recheck Baggage'
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
        return $this->hasOne(Airports::class, ['iata' => 'qs_arrival_airport_code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartureAirport()
    {
        return $this->hasOne(Airports::class, ['iata' => 'qs_departure_airport_code']);
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

        if (($departureTime->format('H') <= 1 || $departureTime->format('H') >= 22)  && $arrivalTime->format('H') <= 7) {
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
            self::CABIN_ECONOMY => 'Economy Basic',
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

    /**
     * @return string
     */
    public function getTicketColor(): string
    {
        return self::TICKET_COLOR_LIST[$this->qs_ticket_id] ?? '#FFFFFF';
    }

    /**
     * @param int $quoteId
     * @param string $departure
     * @param string $arrival
     * @return ActiveRecord|null
     */
    public static function getByQuoteAndIata(int $quoteId, string $departure, string $arrival): ?ActiveRecord
    {
        return self::find()
            ->innerJoin(QuoteTrip::tableName(), 'qs_trip_id = qt_id')
            ->andWhere(['qt_quote_id' => $quoteId])
            ->andWhere(['qs_departure_airport_code' => $departure])
            ->andWhere(['qs_arrival_airport_code' => $arrival])
            ->one();
    }

    public static function createFromSearch(array $segmentEntry, ?int $ticketId): QuoteSegment
    {
        $segment = new self();
        $segment->qs_departure_airport_code = $segmentEntry['departureAirportCode'] ?? null;
        $segment->qs_departure_airport_terminal = $segmentEntry['departureAirportTerminal'] ?? null;
        $segment->qs_arrival_airport_code = $segmentEntry['arrivalAirportCode'] ?? null;
        $segment->qs_arrival_airport_terminal = $segmentEntry['arrivalAirportTerminal'] ?? null;
        $segment->qs_arrival_time = $segmentEntry['arrivalTime'] ?? null;
        $segment->qs_departure_time = $segmentEntry['departureTime'] ?? null;
        $segment->qs_booking_class = $segmentEntry['bookingClass'] ?? null;
        $segment->qs_flight_number = $segmentEntry['flightNumber'] ?? null;
        $segment->qs_fare_code = $segmentEntry['fareCode'] ?? null;
        $segment->qs_duration = $segmentEntry['duration'] ?? null;
        $segment->qs_operating_airline = $segmentEntry['operatingAirline'] ?? null;
        $segment->qs_marketing_airline = $segmentEntry['marketingAirline'] ?? null;
        $segment->qs_cabin = $segmentEntry['cabin'] ?? null;
        $segment->qs_cabin_basic = !empty($segmentEntry['cabinIsBasic']) ? 1 : 0;
        $segment->qs_ticket_id = $ticketId;
        $segment->qs_mileage = $segmentEntry['mileage'] ?? null;
        $segment->qs_marriage_group = $segmentEntry['marriageGroup'] ?? null;
        $segment->qs_meal = $segmentEntry['meal'] ?? null;
        $segment->qs_recheck_baggage = $segmentEntry['recheckBaggage'] ?? null;
        $segment->qs_stop = $segmentEntry['stop'] ?? null;
        $segment->qs_air_equip_type = $segmentEntry['airEquipType'] ?? null;
        $segment->qs_key = '#' . $segment->qs_flight_number .
            ($segment->qs_stop > 0 ? '(' . $segment->qs_stop . ')' : '') .
            $segment->qs_departure_airport_code . '-' . $segment->qs_arrival_airport_code . ' ' . $segment->qs_departure_time;
        return $segment;
    }

    public function generateKey()
    {
        $this->qs_key = '#' . $this->qs_flight_number .
            ($this->qs_stop > 0 ? '(' . $this->qs_stop . ')' : '') .
            $this->qs_departure_airport_code . '-' . $this->qs_arrival_airport_code . ' ' . $this->qs_departure_time;
    }
}
