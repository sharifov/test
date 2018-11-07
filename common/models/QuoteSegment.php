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
 * @property QuoteSegmentStop[] $quoteSegmentStops
 * @property Airport $arrivalAirport
 * @property Airport $departureAirport
 */
class QuoteSegment extends \yii\db\ActiveRecord
{
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
            [['qs_operating_airline', 'qs_marketing_airline', 'qs_marriage_group'], 'string', 'max' => 2],
            [['qs_fare_code'], 'string', 'max' => 15],
            [['qs_key'], 'string', 'max' => 255],
            [['qs_arrival_airport_code'], 'exist', 'skipOnError' => true, 'targetClass' => Airport::className(), 'targetAttribute' => ['qs_arrival_airport_code' => 'iata']],
            [['qs_departure_airport_code'], 'exist', 'skipOnError' => true, 'targetClass' => Airport::className(), 'targetAttribute' => ['qs_departure_airport_code' => 'iata']],
            [['qs_trip_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuoteTrip::className(), 'targetAttribute' => ['qs_trip_id' => 'qt_id']],
            [['qs_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['qs_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qs_id' => 'Qs ID',
            'qs_departure_time' => 'Qs Departure Time',
            'qs_arrival_time' => 'Qs Arrival Time',
            'qs_stop' => 'Qs Stop',
            'qs_flight_number' => 'Qs Flight Number',
            'qs_booking_class' => 'Qs Booking Class',
            'qs_duration' => 'Qs Duration',
            'qs_departure_airport_code' => 'Qs Departure Airport Code',
            'qs_departure_airport_terminal' => 'Qs Departure Airport Terminal',
            'qs_arrival_airport_code' => 'Qs Arrival Airport Code',
            'qs_arrival_airport_terminal' => 'Qs Arrival Airport Terminal',
            'qs_operating_airline' => 'Qs Operating Airline',
            'qs_marketing_airline' => 'Qs Marketing Airline',
            'qs_air_equip_type' => 'Qs Air Equip Type',
            'qs_marriage_group' => 'Qs Marriage Group',
            'qs_mileage' => 'Qs Mileage',
            'qs_cabin' => 'Qs Cabin',
            'qs_meal' => 'Qs Meal',
            'qs_fare_code' => 'Qs Fare Code',
            'qs_trip_id' => 'Qs Trip ID',
            'qs_key' => 'Qs Key',
            'qs_created_dt' => 'Qs Created Dt',
            'qs_updated_dt' => 'Qs Updated Dt',
            'qs_updated_user_id' => 'Qs Updated User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArrivalAirport()
    {
        return $this->hasOne(Airport::className(), ['iata' => 'qs_arrival_airport_code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartureAirport()
    {
        return $this->hasOne(Airport::className(), ['iata' => 'qs_departure_airport_code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrip()
    {
        return $this->hasOne(QuoteTrip::className(), ['qt_id' => 'qs_trip_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedUser()
    {
        return $this->hasOne(Employee::className(), ['id' => 'qs_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuoteSegmentBaggages()
    {
        return $this->hasMany(QuoteSegmentBaggage::className(), ['qsb_segment_id' => 'qs_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuoteSegmentStops()
    {
        return $this->hasMany(QuoteSegmentStop::className(), ['qss_segment_id' => 'qs_id']);
    }
}
