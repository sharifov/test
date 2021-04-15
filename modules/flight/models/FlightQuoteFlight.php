<?php

namespace modules\flight\models;

use common\components\validators\CheckJsonValidator;
use sales\behaviors\StringToJsonBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "flight_quote_flight".
 *
 * @property int $fqf_id
 * @property int|null $fqf_fq_id
 * @property int|null $fqf_trip_type_id
 * @property string|null $fqf_main_airline
 * @property int|null $fqf_status_id
 * @property string|null $fqf_booking_id
 * @property string|null $fqf_pnr
 * @property string|null $fqf_validating_carrier
 * @property string|null $fqf_original_data_json
 * @property string|null $fqf_created_dt
 * @property string|null $fqf_updated_dt
 *
 * @property FlightQuotePaxPrice[] $flightQuotePaxPrices
 * @property FlightQuoteSegmentStop[] $flightQuoteSegmentStops
 * @property FlightQuoteSegment[] $flightQuoteSegments
 * @property FlightQuoteTrip[] $flightQuoteTrips
 * @property FlightQuote $fqfFq
 * @property FlightQuoteBooking[] $flightQuoteBookings
 */
class FlightQuoteFlight extends \yii\db\ActiveRecord
{
    public const TRIP_TYPE_OW = 1;
    public const TRIP_TYPE_RT = 2;
    public const TRIP_TYPE_MC = 3;

    public const TRIP_TYPE_LIST = [
        self::TRIP_TYPE_OW => 'OW',
        self::TRIP_TYPE_RT => 'RT',
        self::TRIP_TYPE_MC => 'MC',
    ];

    public function rules(): array
    {
        return [
            [['fqf_fq_id'], 'required'],

            ['fqf_fq_id', 'integer'],
            ['fqf_fq_id', 'exist', 'skipOnError' => true, 'targetClass' => FlightQuote::class, 'targetAttribute' => ['fqf_fq_id' => 'fq_id']],

            ['fqf_booking_id', 'string', 'max' => 50],
            ['fqf_main_airline', 'string', 'max' => 2],
            ['fqf_pnr', 'string', 'max' => 10],
            ['fqf_validating_carrier', 'string', 'max' => 2],

            ['fqf_status_id', 'integer'],
            ['fqf_trip_type_id', 'integer'],

            [['fqf_created_dt', 'fqf_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['fqf_original_data_json', CheckJsonValidator::class],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['fqf_created_dt', 'fqf_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['fqf_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'stringToJson' => [
                'class' => StringToJsonBehavior::class,
                'jsonColumn' => 'fqf_original_data_json',
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getFlightQuotePaxPrices(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FlightQuotePaxPrice::class, ['qpp_flight_id' => 'fqf_id']);
    }

    public function getFlightQuoteSegmentStops(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FlightQuoteSegmentStop::class, ['qss_flight_id' => 'fqf_id']);
    }

    public function getFlightQuoteSegments(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FlightQuoteSegment::class, ['fqs_flight_id' => 'fqf_id']);
    }

    public function getFlightQuoteTrips(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FlightQuoteTrip::class, ['fqp_flight_id' => 'fqf_id']);
    }

    public function getFlightQuoteBookings(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FlightQuoteBooking::class, ['fqb_fqf_id' => 'fqf_id']);
    }

    public function getFqfFq(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FlightQuote::class, ['fq_id' => 'fqf_fq_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fqf_id' => 'ID',
            'fqf_fq_id' => 'FlightQuote ID',
            'fqf_trip_type_id' => 'Trip Type ID',
            'fqf_main_airline' => 'Main Airline',
            'fqf_status_id' => 'Status ID',
            'fqf_booking_id' => 'Booking ID',
            'fqf_pnr' => 'Pnr',
            'fqf_validating_carrier' => 'Validating Carrier',
            'fqf_original_data_json' => 'Original Data Json',
            'fqf_created_dt' => 'Created Dt',
            'fqf_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): \modules\flight\models\query\FlightQuoteFlightQuery
    {
        return new \modules\flight\models\query\FlightQuoteFlightQuery(static::class);
    }

    public static function tableName(): string
    {
        return 'flight_quote_flight';
    }

    public static function create(
        int $flightQuoteId,
        ?int $tripTypeId,
        ?string $mainAirline
    ): FlightQuoteFlight {
        $model = new self();
        $model->fqf_fq_id = $flightQuoteId;
        $model->fqf_trip_type_id = $tripTypeId;
        $model->fqf_main_airline = $mainAirline;
        return $model;
    }
}
