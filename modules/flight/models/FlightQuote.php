<?php

namespace modules\flight\models;

use common\models\Employee;
use common\models\ProductQuote;
use sales\interfaces\QuoteCommunicationInterface;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use modules\flight\models\query\FlightQuoteQuery;


/**
 * This is the model class for table "flight_quote".
 *
 * @property int $fq_id
 * @property int $fq_flight_id
 * @property int|null $fq_source_id
 * @property int|null $fq_product_quote_id
 * @property string|null $fq_hash_key
 * @property float|null $fq_service_fee_percent
 * @property string|null $fq_record_locator
 * @property string|null $fq_gds
 * @property string|null $fq_gds_pcc
 * @property int|null $fq_gds_offer_id
 * @property int|null $fq_type_id
 * @property string|null $fq_cabin_class
 * @property int|null $fq_trip_type_id
 * @property string|null $fq_main_airline
 * @property int|null $fq_fare_type_id
 * @property int|null $fq_created_user_id
 * @property int|null $fq_created_expert_id
 * @property string|null $fq_created_expert_name
 * @property string|null $fq_reservation_dump
 * @property string|null $fq_pricing_info
 * @property string|null $fq_origin_search_data
 * @property string|null $fq_last_ticket_date
 *
 * @property Employee $fqCreatedUser
 * @property Flight $fqFlight
 * @property ProductQuote $fqProductQuote
 * @property FlightQuotePaxPrice[] $flightQuotePaxPrices
 * @property FlightQuoteSegment[] $flightQuoteSegments
 * @property FlightQuoteStatusLog[] $flightQuoteStatusLogs
 * @property array $extraData
 * @property FlightQuoteTrip[] $flightQuoteTrips
 */
class FlightQuote extends ActiveRecord implements QuoteCommunicationInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_quote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fq_flight_id'], 'required'],
            [['fq_flight_id', 'fq_source_id', 'fq_product_quote_id', 'fq_gds_offer_id', 'fq_type_id', 'fq_trip_type_id', 'fq_fare_type_id', 'fq_created_user_id', 'fq_created_expert_id'], 'integer'],
            [['fq_service_fee_percent'], 'number'],
            [['fq_reservation_dump', 'fq_pricing_info'], 'string'],
            [['fq_origin_search_data', 'fq_last_ticket_date'], 'safe'],
            [['fq_hash_key'], 'string', 'max' => 32],
            [['fq_record_locator'], 'string', 'max' => 8],
            [['fq_gds', 'fq_main_airline'], 'string', 'max' => 2],
            [['fq_gds_pcc'], 'string', 'max' => 10],
            [['fq_cabin_class'], 'string', 'max' => 1],
            [['fq_created_expert_name'], 'string', 'max' => 20],
            [['fq_hash_key'], 'unique'],
            [['fq_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['fq_created_user_id' => 'id']],
            [['fq_flight_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flight::class, 'targetAttribute' => ['fq_flight_id' => 'fl_id']],
            [['fq_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['fq_product_quote_id' => 'pq_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fq_id' => 'ID',
            'fq_flight_id' => 'Flight ID',
            'fq_source_id' => 'Source ID',
            'fq_product_quote_id' => 'Product Quote ID',
            'fq_hash_key' => 'Hash Key',
            'fq_service_fee_percent' => 'Service Fee Percent',
            'fq_record_locator' => 'Record Locator',
            'fq_gds' => 'Gds',
            'fq_gds_pcc' => 'Gds Pcc',
            'fq_gds_offer_id' => 'Gds Offer ID',
            'fq_type_id' => 'Type ID',
            'fq_cabin_class' => 'Cabin Class',
            'fq_trip_type_id' => 'Trip Type ID',
            'fq_main_airline' => 'Main Airline',
            'fq_fare_type_id' => 'Fare Type ID',
            'fq_created_user_id' => 'Created User ID',
            'fq_created_expert_id' => 'Created Expert ID',
            'fq_created_expert_name' => 'Created Expert Name',
            'fq_reservation_dump' => 'Reservation Dump',
            'fq_pricing_info' => 'Pricing Info',
            'fq_origin_search_data' => 'Origin Search Data',
            'fq_last_ticket_date' => 'Last Ticket Date',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getFqCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'fq_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFqFlight()
    {
        return $this->hasOne(Flight::class, ['fl_id' => 'fq_flight_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFqProductQuote()
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'fq_product_quote_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFlightQuotePaxPrices()
    {
        return $this->hasMany(FlightQuotePaxPrice::class, ['qpp_flight_quote_id' => 'fq_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFlightQuoteSegments()
    {
        return $this->hasMany(FlightQuoteSegment::class, ['fqs_flight_quote_id' => 'fq_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFlightQuoteStatusLogs(): \yii\db\ActiveQuery
	{
        return $this->hasMany(FlightQuoteStatusLog::class, ['qsl_flight_quote_id' => 'fq_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFlightQuoteTrips(): \yii\db\ActiveQuery
	{
        return $this->hasMany(FlightQuoteTrip::class, ['fqt_flight_quote_id' => 'fq_id']);
    }

    /**
     * {@inheritdoc}
     * @return FlightQuoteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FlightQuoteQuery(static::class);
    }

    /**
     * @return array
     */
    public function getExtraData(): array
    {
        return []; // TODO: Implement getExtraData() method.
    }

}
