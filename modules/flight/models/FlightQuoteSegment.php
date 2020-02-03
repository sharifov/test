<?php

namespace modules\flight\models;

use modules\flight\src\entities\flightQuoteSegment\serializer\FlightQuoteSegmentSerializer;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentDTO;
use Yii;

/**
 * This is the model class for table "flight_quote_segment".
 *
 * @property int $fqs_id
 * @property int $fqs_flight_quote_id
 * @property int|null $fqs_flight_quote_trip_id
 * @property string $fqs_departure_dt
 * @property string $fqs_arrival_dt
 * @property int|null $fqs_stop
 * @property int|null $fqs_flight_number
 * @property string|null $fqs_booking_class
 * @property int|null $fqs_duration
 * @property string $fqs_departure_airport_iata
 * @property string|null $fqs_departure_airport_terminal
 * @property string $fqs_arrival_airport_iata
 * @property string|null $fqs_arrival_airport_terminal
 * @property string|null $fqs_operating_airline
 * @property string|null $fqs_marketing_airline
 * @property string|null $fqs_air_equip_type
 * @property string|null $fqs_marriage_group
 * @property string|null $fqs_cabin_class
 * @property string|null $fqs_meal
 * @property string|null $fqs_fare_code
 * @property string|null $fqs_key
 * @property int|null $fqs_ticket_id
 * @property int|null $fqs_recheck_baggage
 * @property int|null $fqs_mileage
 *
 * @property FlightQuote $fqsFlightQuote
 * @property FlightQuoteTrip $fqsFlightQuoteTrip
 * @property FlightQuoteSegmentPaxBaggage[] $flightQuoteSegmentPaxBaggages
 * @property FlightQuoteSegmentPaxBaggageCharge[] $flightQuoteSegmentPaxBaggageCharges
 * @property FlightQuoteSegmentStop[] $flightQuoteSegmentStops
 */
class FlightQuoteSegment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_quote_segment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fqs_flight_quote_id', 'fqs_departure_dt', 'fqs_arrival_dt', 'fqs_departure_airport_iata', 'fqs_arrival_airport_iata'], 'required'],
            [['fqs_flight_quote_id', 'fqs_flight_quote_trip_id', 'fqs_stop', 'fqs_flight_number', 'fqs_duration', 'fqs_ticket_id', 'fqs_recheck_baggage', 'fqs_mileage'], 'integer'],
            [['fqs_departure_dt', 'fqs_arrival_dt'], 'safe'],
            [['fqs_booking_class'], 'string', 'max' => 1],
            [['fqs_departure_airport_iata', 'fqs_departure_airport_terminal', 'fqs_arrival_airport_iata', 'fqs_arrival_airport_terminal'], 'string', 'max' => 3],
            [['fqs_operating_airline', 'fqs_marketing_airline', 'fqs_marriage_group', 'fqs_cabin_class', 'fqs_meal'], 'string', 'max' => 2],
            [['fqs_air_equip_type'], 'string', 'max' => 4],
            [['fqs_fare_code'], 'string', 'max' => 20],
            [['fqs_key'], 'string', 'max' => 40],
            [['fqs_flight_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuote::class, 'targetAttribute' => ['fqs_flight_quote_id' => 'fq_id']],
            [['fqs_flight_quote_trip_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteTrip::class, 'targetAttribute' => ['fqs_flight_quote_trip_id' => 'fqt_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fqs_id' => 'Fqs ID',
            'fqs_flight_quote_id' => 'Fqs Flight Quote ID',
            'fqs_flight_quote_trip_id' => 'Fqs Flight Quote Trip ID',
            'fqs_departure_dt' => 'Fqs Departure Dt',
            'fqs_arrival_dt' => 'Fqs Arrival Dt',
            'fqs_stop' => 'Fqs Stop',
            'fqs_flight_number' => 'Fqs Flight Number',
            'fqs_booking_class' => 'Fqs Booking Class',
            'fqs_duration' => 'Fqs Duration',
            'fqs_departure_airport_iata' => 'Fqs Departure Airport Iata',
            'fqs_departure_airport_terminal' => 'Fqs Departure Airport Terminal',
            'fqs_arrival_airport_iata' => 'Fqs Arrival Airport Iata',
            'fqs_arrival_airport_terminal' => 'Fqs Arrival Airport Terminal',
            'fqs_operating_airline' => 'Fqs Operating Airline',
            'fqs_marketing_airline' => 'Fqs Marketing Airline',
            'fqs_air_equip_type' => 'Fqs Air Equip Type',
            'fqs_marriage_group' => 'Fqs Marriage Group',
            'fqs_cabin_class' => 'Fqs Cabin Class',
            'fqs_meal' => 'Fqs Meal',
            'fqs_fare_code' => 'Fqs Fare Code',
            'fqs_key' => 'Fqs Key',
            'fqs_ticket_id' => 'Fqs Ticket ID',
            'fqs_recheck_baggage' => 'Fqs Recheck Baggage',
            'fqs_mileage' => 'Fqs Mileage',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFqsFlightQuote()
    {
        return $this->hasOne(FlightQuote::class, ['fq_id' => 'fqs_flight_quote_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFqsFlightQuoteTrip()
    {
        return $this->hasOne(FlightQuoteTrip::class, ['fqt_id' => 'fqs_flight_quote_trip_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlightQuoteSegmentPaxBaggages()
    {
        return $this->hasMany(FlightQuoteSegmentPaxBaggage::class, ['qsb_flight_quote_segment_id' => 'fqs_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlightQuoteSegmentPaxBaggageCharges()
    {
        return $this->hasMany(FlightQuoteSegmentPaxBaggageCharge::class, ['qsbc_flight_quote_segment_id' => 'fqs_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlightQuoteSegmentStops()
    {
        return $this->hasMany(FlightQuoteSegmentStop::class, ['qss_quote_segment_id' => 'fqs_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\query\FlightQuoteSegmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\flight\models\query\FlightQuoteSegmentQuery(static::class);
    }

	/**
	 * @param FlightQuoteSegmentDTO $dto
	 * @return FlightQuoteSegment
	 */
    public static function create(FlightQuoteSegmentDTO $dto): self
	{
		$segment = new self();

		$segment->fqs_flight_quote_id = $dto->flightQuoteId;
		$segment->fqs_flight_quote_trip_id = $dto->flightQuoteTripId;
		$segment->fqs_departure_dt = $dto->departureDt;
		$segment->fqs_arrival_dt = $dto->arrivalDt;
		$segment->fqs_stop = $dto->stop;
		$segment->fqs_flight_number = $dto->flightNumber;
		$segment->fqs_booking_class = $dto->bookingClass;
		$segment->fqs_duration = $dto->duration;
		$segment->fqs_departure_airport_iata = $dto->departureAirportIata;
		$segment->fqs_departure_airport_terminal = $dto->departureAirportTerminal;
		$segment->fqs_arrival_airport_iata = $dto->arrivalAirportIata;
		$segment->fqs_arrival_airport_terminal = $dto->arrivalAirportTerminal;
		$segment->fqs_operating_airline = $dto->operatingAirline;
		$segment->fqs_marketing_airline = $dto->marketingAirline;
		$segment->fqs_air_equip_type = $dto->airEquipType;
		$segment->fqs_marriage_group = $dto->marriageGroup;
		$segment->fqs_cabin_class = $dto->cabinClass;
		$segment->fqs_meal = $dto->meal;
		$segment->fqs_fare_code = $dto->fareCode;
		$segment->fqs_key = $dto->key;
		$segment->fqs_ticket_id = $dto->ticketId;
		$segment->fqs_recheck_baggage = $dto->recheckBaggage;
		$segment->fqs_mileage = $dto->mileage;

		return $segment;
	}

    public function serialize(): array
    {
        return (new FlightQuoteSegmentSerializer($this))->getData();
	}
}
