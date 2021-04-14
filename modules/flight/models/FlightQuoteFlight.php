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
 * @property string|null $fqf_record_locator
 * @property string|null $fqf_gds
 * @property string|null $fqf_gds_pcc
 * @property int|null $fqf_type_id
 * @property string|null $fqf_cabin_class
 * @property int|null $fqf_trip_type_id
 * @property string|null $fqf_main_airline
 * @property int|null $fqf_fare_type_id
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
 * @property FlightQuoteTicket[] $flightQuoteTickets
 * @property FlightQuoteTrip[] $flightQuoteTrips
 * @property FlightQuote $fqfFq
 * @property FlightPax[] $fqtPaxes
 */
class FlightQuoteFlight extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['fqf_fq_id'], 'required'],

            ['fqf_fare_type_id', 'integer'],

            ['fqf_fq_id', 'integer'],
            ['fqf_fq_id', 'exist', 'skipOnError' => true, 'targetClass' => FlightQuote::class, 'targetAttribute' => ['fqf_fq_id' => 'fq_id']],

            ['fqf_booking_id', 'string', 'max' => 50],
            ['fqf_cabin_class', 'string', 'max' => 1],
            ['fqf_gds', 'string', 'max' => 2],
            ['fqf_gds_pcc', 'string', 'max' => 10],
            ['fqf_main_airline', 'string', 'max' => 2],
            ['fqf_pnr', 'string', 'max' => 10],
            ['fqf_record_locator', 'string', 'max' => 8],
            ['fqf_validating_carrier', 'string', 'max' => 2],

            ['fqf_status_id', 'integer'],
            ['fqf_trip_type_id', 'integer'],
            ['fqf_type_id', 'integer'],

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

    public function getFlightQuoteTickets(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FlightQuoteTicket::class, ['fqt_flight_id' => 'fqf_id']);
    }

    public function getFlightQuoteTrips(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FlightQuoteTrip::class, ['fqp_flight_id' => 'fqf_id']);
    }

    public function getFqfFq(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FlightQuote::class, ['fq_id' => 'fqf_fq_id']);
    }

    public function getFqtPaxes(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FlightPax::class, ['fp_id' => 'fqt_pax_id'])->viaTable('flight_quote_ticket', ['fqt_flight_id' => 'fqf_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fqf_id' => 'ID',
            'fqf_fq_id' => 'FlightQuote ID',
            'fqf_record_locator' => 'Record Locator',
            'fqf_gds' => 'Gds',
            'fqf_gds_pcc' => 'Gds Pcc',
            'fqf_type_id' => 'Type ID',
            'fqf_cabin_class' => 'Cabin Class',
            'fqf_trip_type_id' => 'Trip Type ID',
            'fqf_main_airline' => 'Main Airline',
            'fqf_fare_type_id' => 'Fare Type ID',
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
        ?string $recordLocator,
        ?string $gds,
        ?string $gdsPcc,
        ?int $typeId,
        ?string $cabinClass,
        ?int $tripTypeId,
        ?string $mainAirline,
        ?int $fareTypeId
    ): FlightQuoteFlight {
        $model = new self();
        $model->fqf_fq_id = $flightQuoteId;
        $model->fqf_record_locator = $recordLocator;
        $model->fqf_gds = $gds;
        $model->fqf_gds_pcc = $gdsPcc;
        $model->fqf_type_id = $typeId;
        $model->fqf_cabin_class = $cabinClass;
        $model->fqf_trip_type_id = $tripTypeId;
        $model->fqf_main_airline = $mainAirline;
        $model->fqf_fare_type_id = $fareTypeId;
        return $model;
    }
}
