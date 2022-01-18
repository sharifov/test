<?php

namespace modules\flight\models;

use common\components\validators\CheckJsonValidator;
use common\models\Airline;
use common\models\Client;
use common\models\Lead;
use common\models\Project;
use modules\flight\models\query\FlightQuoteFlightQuery;
use modules\flight\src\entities\flightQuoteFlight\serializer\FlightQuoteFlightSerializer;
use modules\order\src\entities\order\Order;
use modules\product\src\interfaces\ProductDataInterface;
use src\behaviors\StringToJsonBehavior;
use src\services\caseSale\PnrPreparingService;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
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
 * @property string|null $fqf_child_booking_id
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
 * @property Airline $mainAirline
 */
class FlightQuoteFlight extends ActiveRecord implements ProductDataInterface
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
            ['fqf_child_booking_id', 'string', 'max' => 50],
            ['fqf_main_airline', 'string', 'max' => 2],
            ['fqf_validating_carrier', 'string', 'max' => 2],

            ['fqf_status_id', 'integer'],

            ['fqf_trip_type_id', 'integer'],
            ['fqf_trip_type_id', 'in', 'range' => array_keys(self::TRIP_TYPE_LIST)],

            [['fqf_created_dt', 'fqf_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['fqf_original_data_json', CheckJsonValidator::class],

            ['fqf_pnr', 'string', 'max' => 70],
            [['fqf_pnr'], 'filter', 'filter' => static function ($value) {
                return empty($value) ? $value : (new PnrPreparingService($value))->getPnr();
            }],
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

    public function getFlightQuotePaxPrices(): ActiveQuery
    {
        return $this->hasMany(FlightQuotePaxPrice::class, ['qpp_flight_id' => 'fqf_id']);
    }

    public function getFlightQuoteSegmentStops(): ActiveQuery
    {
        return $this->hasMany(FlightQuoteSegmentStop::class, ['qss_flight_id' => 'fqf_id']);
    }

    public function getFlightQuoteSegments(): ActiveQuery
    {
        return $this->hasMany(FlightQuoteSegment::class, ['fqs_flight_id' => 'fqf_id']);
    }

    public function getFlightQuoteTrips(): ActiveQuery
    {
        return $this->hasMany(FlightQuoteTrip::class, ['fqt_flight_id' => 'fqf_id']);
    }

    public function getFlightQuoteBookings(): ActiveQuery
    {
        return $this->hasMany(FlightQuoteBooking::class, ['fqb_fqf_id' => 'fqf_id']);
    }

    public function getFqfFq(): ActiveQuery
    {
        return $this->hasOne(FlightQuote::class, ['fq_id' => 'fqf_fq_id']);
    }

    public function getMainAirline(): ActiveQuery
    {
        return $this->hasOne(Airline::class, ['iata' => 'fqf_main_airline']);
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
            'fqf_child_booking_id' => 'Child Booking ID',
            'fqf_pnr' => 'Pnr',
            'fqf_validating_carrier' => 'Validating Carrier',
            'fqf_original_data_json' => 'Original Data Json',
            'fqf_created_dt' => 'Created Dt',
            'fqf_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): FlightQuoteFlightQuery
    {
        return new FlightQuoteFlightQuery(static::class);
    }

    public static function tableName(): string
    {
        return 'flight_quote_flight';
    }

    public static function create(
        int $flightQuoteId,
        ?int $tripTypeId,
        ?string $mainAirline,
        ?string $bookingId,
        ?int $statusId,
        ?string $pnr,
        ?string $validatingCarrier,
        $originalDataJson,
        ?string $childBookingId = null
    ): FlightQuoteFlight {
        $model = new self();
        $model->fqf_fq_id = $flightQuoteId;
        $model->fqf_trip_type_id = $tripTypeId;
        $model->fqf_main_airline = $mainAirline;
        $model->fqf_booking_id = $bookingId;
        $model->fqf_child_booking_id = $childBookingId;
        $model->fqf_status_id = $statusId;
        $model->fqf_pnr = $pnr;
        $model->fqf_validating_carrier = $validatingCarrier;
        $model->fqf_original_data_json = $originalDataJson;
        return $model;
    }

    public function getProject(): ?Project
    {
        if (($order = $this->getOrder()) && $project = $order->getProject()) {
            return $project;
        }
        return $this->fqfFq->getProject();
    }

    public function getLead(): ?Lead
    {
        return $this->fqfFq->getLead();
    }

    public function getClient(): ?Client
    {
        return $this->fqfFq->getClient();
    }

    public function getOrder(): ?Order
    {
        return $this->fqfFq->getOrder();
    }

    public function getId(): int
    {
        return $this->fqf_id;
    }

    public function serialize(bool $withBooking = true): array
    {
        return (new FlightQuoteFlightSerializer($this))->getData($withBooking);
    }

    public function fields(): array
    {
        $fields = [
            'fqf_trip_type_id',
            'fqf_main_airline',
            'fqf_booking_id',
            'fqf_pnr',
            'fqf_validating_carrier',
        ];
        $fields['bookings'] = function () {
            $bookings = [];
            if ($this->flightQuoteBookings) {
                foreach ($this->flightQuoteBookings as $keyBooking => $flightQuoteBooking) {
                    $bookings[$keyBooking] = $flightQuoteBooking->toArray();
                }
            }
            return $bookings;
        };
        $fields['flight_quote']['fq_type_name'] = function () {
            return FlightQuote::getTypeName($this->fqfFq->fq_type_id);
        };
        $fields['flight_quote']['fq_fare_type_name'] = function () {
            return FlightQuote::getFareTypeNameById($this->fqfFq->fq_fare_type_id);
        };
        $fields['flight'] = function () {
            return $this->fqfFq->fqFlight ? $this->fqfFq->fqFlight->toArray() : [];
        };
        $fields['trips'] = function () {
            $trips = [];
            if ($this->flightQuoteTrips) {
                foreach ($this->flightQuoteTrips as $keyTrip => $flightQuoteTrip) {
                    $trips[$keyTrip] = $flightQuoteTrip->toArray();
                    foreach ($flightQuoteTrip->flightQuoteSegments as $keySegment => $flightQuoteSegment) {
                        $trips[$keyTrip]['segments'][$keySegment] = $flightQuoteSegment->toArray();
                    }
                }
            }
            return $trips;
        };
        return $fields;
    }

    public static function clone(FlightQuoteFlight $originFlightQuoteFlight, int $flightQuoteId): self
    {
        $clone = new self();
        $clone->attributes = $originFlightQuoteFlight->attributes;
        $clone->fqf_fq_id = $flightQuoteId;
        return $clone;
    }
}
