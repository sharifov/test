<?php

namespace modules\flight\models;

use common\components\SearchService;
use common\models\Airline;
use common\models\Employee;
use modules\flight\models\behaviors\FlightQuoteFqUid;
use modules\flight\src\entities\flightQuote\events\FlightQuoteCloneCreatedEvent;
use modules\flight\src\entities\flightQuote\serializer\FlightQuoteSerializer;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteCreateDTO;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\interfaces\Quotable;
use sales\entities\EventTrait;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use modules\flight\src\entities\flightQuote\Scopes;
use yii\helpers\ArrayHelper;

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
 * @property string|null $fq_request_hash
 * @property string|null $fq_uid
 * @property array|null $fq_json_booking
 * @property string|null $fq_flight_request_uid
 *
 * @property Employee $fqCreatedUser
 * @property Flight $fqFlight
 * @property ProductQuote $fqProductQuote
 * @property FlightQuotePaxPrice[] $flightQuotePaxPrices
 * @property FlightQuoteSegment[] $flightQuoteSegments
 * @property FlightQuoteStatusLog[] $flightQuoteStatusLogs
 * @property FlightQuoteTrip[] $flightQuoteTrips
 * @property Airline $mainAirline
 */
class FlightQuote extends ActiveRecord implements Quotable
{
    use EventTrait;

    public const FARE_TYPE_PUBLIC = 'PUB';
    public const FARE_TYPE_PRIVATE = 'SR';
    public const FARE_TYPE_COMMISSION = 'COMM';
    public const FARE_TYPE_TOUR = 'TOUR';

    public const FARE_TYPE_LIST = [
        self::FARE_TYPE_PUBLIC => 'Public',
        self::FARE_TYPE_PRIVATE => 'Private',
        self::FARE_TYPE_COMMISSION => 'Commission',
        self::FARE_TYPE_TOUR => 'Tour',
    ];

    public const FARE_TYPE_ID_LIST = [
        self::FARE_TYPE_PUBLIC => 1,
        self::FARE_TYPE_PRIVATE => 2,
        self::FARE_TYPE_COMMISSION => 3,
        self::FARE_TYPE_TOUR => 4
    ];

    public const STOPS_DIRECT = 0;
    public const STOPS_UP_TO_1 = 1;
    public const STOPS_UP_TO_2 = 2;

    public const STOPS_LIST = [
        self::STOPS_DIRECT => 'Direct only',
        self::STOPS_UP_TO_1 => 'Up to 1 stop',
        self::STOPS_UP_TO_2 => 'Up to 2 stop'
    ];

    public const CHANGE_AIRPORT_ANY = 0;
    public const CHANGE_AIRPORT_NO = 1;

    public const CHANGE_AIRPORT_LIST = [
        self::CHANGE_AIRPORT_ANY => '--',
        self::CHANGE_AIRPORT_NO => 'No Airport Change'
    ];

    public const BAGGAGE_ANY = 0;
    public const BAGGAGE_ONE_PLUS = 1;
    public const BAGGAGE_TWO_PLUS = 2;

    public const BAGGAGE_LIST = [
        self::BAGGAGE_ANY => '--',
        self::BAGGAGE_ONE_PLUS => '1+',
        self::BAGGAGE_TWO_PLUS => '2+'
    ];

    public const SORT_BY_PRICE_ASC = 'price_asc';
    public const SORT_BY_PRICE_DESC = 'price_desc';
    public const SORT_BY_DURATION_ASC = 'duration_asc';
    public const SORT_BY_DURATION_DESC = 'duration_desc';

    public const SORT_BY_LIST = [
        self::SORT_BY_PRICE_ASC => 'Price (ASC)',
        self::SORT_BY_PRICE_DESC => 'Price (DESC)',
        self::SORT_BY_DURATION_ASC => 'Destination (ASC)',
        self::SORT_BY_DURATION_DESC => 'Destination (DESC)',
    ];

    public const SORT_TYPE_LIST = [
        self::SORT_BY_PRICE_ASC => SORT_ASC,
        self::SORT_BY_PRICE_DESC => SORT_DESC,
        self::SORT_BY_DURATION_ASC => SORT_ASC,
        self::SORT_BY_DURATION_DESC => SORT_DESC
    ];

    public const SORT_ATTRIBUTES_NAME_LIST = [
        self::SORT_BY_PRICE_ASC => 'price',
        self::SORT_BY_PRICE_DESC    => 'price',
        self::SORT_BY_DURATION_ASC  => 'duration',
        self::SORT_BY_DURATION_DESC => 'duration',
    ];

    public const TYPE_BASE = 0;
    public const TYPE_ORIGINAL = 1;
    public const TYPE_ALTERNATIVE = 2;

    public const TYPE_LIST = [
        self::TYPE_BASE => 'Base',
        self::TYPE_ORIGINAL => 'Original',
        self::TYPE_ALTERNATIVE => 'Alternative',
    ];

    public const SERVICE_FEE = 0.035;

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
            [['fq_flight_id', 'fq_source_id', 'fq_product_quote_id', 'fq_type_id', 'fq_trip_type_id', 'fq_fare_type_id', 'fq_created_user_id', 'fq_created_expert_id'], 'integer'],
            [['fq_service_fee_percent'], 'number'],
            [['fq_reservation_dump', 'fq_pricing_info', 'fq_gds_offer_id'], 'string'],
            [['fq_origin_search_data', 'fq_last_ticket_date'], 'safe'],
            [['fq_hash_key', 'fq_request_hash'], 'string', 'max' => 32],
            [['fq_record_locator'], 'string', 'max' => 8],
            [['fq_gds', 'fq_main_airline'], 'string', 'max' => 2],
            [['fq_gds_pcc'], 'string', 'max' => 10],
            [['fq_cabin_class'], 'string', 'max' => 1],
            [['fq_created_expert_name'], 'string', 'max' => 20],
            [['fq_uid'], 'string', 'max' => 50],
            [['fq_hash_key'], 'unique', 'targetAttribute' => ['fq_flight_id', 'fq_hash_key'] , 'message' => 'Flight already have this quote;', 'skipOnEmpty' => true],
            [['fq_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['fq_created_user_id' => 'id']],
            [['fq_flight_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flight::class, 'targetAttribute' => ['fq_flight_id' => 'fl_id']],
            [['fq_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['fq_product_quote_id' => 'pq_id']],
            [['fq_json_booking'], 'safe'],
            [['fq_flight_request_uid'], 'string', 'max' => 100],
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
            'fq_request_hash' => 'Request Hash',
            'fq_uid' => 'Uid',
            'fq_json_booking' => 'Json booking',
            'fq_flight_request_uid' => 'Hybrid UID',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'fq_uid' => [
                'class' => FlightQuoteFqUid::class,
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
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
    public function getMainAirline(): ActiveQuery
    {
        return $this->hasOne(Airline::class, ['iata' => 'fq_main_airline']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFlightQuotePaxPrices()
    {
        return $this->hasMany(FlightQuotePaxPrice::class, ['qpp_flight_quote_id' => 'fq_id'])->orderBy(['qpp_flight_pax_code_id' => SORT_ASC]);
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

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    /**
     * @return array
     */
    public static function getFareTypeList(): array
    {
        return self::FARE_TYPE_LIST;
    }

    public static function getFareTypeName(?string $fareType): string
    {
        return self::getFareTypeList()[$fareType] ?? '--';
    }

    public static function getFareTypeNameById(?int $id): string
    {
        return self::getFareTypeName((string)array_search($id, self::getFareTypeIdList(), false));
    }

    public static function getFareTypeIdList(): array
    {
        return self::FARE_TYPE_ID_LIST;
    }

    /**
     * @return array
     */
    public static function getStopsLIst(): array
    {
        return self::STOPS_LIST;
    }

    /**
     * @return array
     */
    public static function getChangeAirportList(): array
    {
        return self::CHANGE_AIRPORT_LIST;
    }

    /**
     * @return array
     */
    public static function getBaggageList(): array
    {
        return self::BAGGAGE_LIST;
    }

    /**
     * @return array
     */
    public static function getSortList(): array
    {
        return self::SORT_BY_LIST;
    }

    /**
     * @return array
     */
    public static function getSortTypeList(): array
    {
        return self::SORT_TYPE_LIST;
    }

    /**
     * @param $sortId
     * @return int|null
     */
    public static function getSortTypeBySortId($sortId): ?int
    {
        return self::getSortTypeList()[$sortId] ?? null;
    }

    /**
     * @return mixed
     */
    public static function getDefaultSortType()
    {
        return self::getSortTypeList()[self::SORT_BY_PRICE_ASC];
    }

    public static function getDefaultSortAttributeName()
    {
        return self::getSortAttributesNameList()[self::SORT_BY_PRICE_ASC];
    }

    /**
     * @return array
     */
    public static function getSortAttributesNameList(): array
    {
        return self::SORT_ATTRIBUTES_NAME_LIST;
    }

    /**
     * @param $sortId
     * @return string|null
     */
    public static function getSortAttributeNameById($sortId): ?string
    {
        return self::getSortAttributesNameList()[$sortId] ?? null;
    }

    /**
     * @param string $fareType
     * @return int|null
     */
    public static function getFareTypeId(string $fareType): ?int
    {
        return self::FARE_TYPE_ID_LIST[$fareType] ?? null;
    }

    /**
     * @param FlightQuoteCreateDTO $dto
     * @return FlightQuote
     */
    public static function create(FlightQuoteCreateDTO $dto): FlightQuote
    {
        $flightQuote = new self();

        $flightQuote->fq_flight_id = $dto->flightId;
        $flightQuote->fq_source_id = $dto->sourceId;
        $flightQuote->fq_product_quote_id = $dto->productQuoteId;
        $flightQuote->fq_hash_key = $dto->hashKey;
        $flightQuote->fq_service_fee_percent = $dto->serviceFeePercent;
        $flightQuote->fq_record_locator = $dto->recordLocator;
        $flightQuote->fq_gds = $dto->gds;
        $flightQuote->fq_gds_offer_id = $dto->gdsOfferId;
        $flightQuote->fq_gds_pcc = $dto->gdsPcc;
        $flightQuote->fq_type_id = $dto->typeId;
        $flightQuote->fq_cabin_class = $dto->cabinClass;
        $flightQuote->fq_trip_type_id = $dto->tripTypeId;
        $flightQuote->fq_main_airline = $dto->mainAirline;
        $flightQuote->fq_fare_type_id = $dto->fareType;
        $flightQuote->fq_created_user_id = $dto->createdUserId;
        $flightQuote->fq_created_expert_id = $dto->createdExpertId;
        $flightQuote->fq_created_expert_name = $dto->createdExpertName;
        $flightQuote->fq_reservation_dump = $dto->reservationDump;
        $flightQuote->fq_pricing_info = $dto->pricingInfo;
        $flightQuote->fq_origin_search_data = $dto->originSearchData;
        $flightQuote->fq_last_ticket_date = $dto->lastTicketDate;
        $flightQuote->fq_request_hash = $dto->requestHash;
        return $flightQuote;
    }

    public static function clone(FlightQuote $quote, int $flightId, int $productQuoteId): self
    {
        $clone = new self();

        $clone->attributes = $quote->attributes;

        $clone->fq_id = null;
        $clone->fq_hash_key = null;
        $clone->fq_flight_id = $flightId;
        $clone->fq_product_quote_id = $productQuoteId;
        $clone->recordEvent(new FlightQuoteCloneCreatedEvent($clone));

        return $clone;
    }

    /**
     * @param $type
     * @return mixed|string
     */
    public static function getTypeName(?int $type)
    {
        return self::TYPE_LIST[$type] ?? '-';
    }

    public function isBase(): bool
    {
        return $this->fq_type_id === self::TYPE_BASE;
    }

    public function base(): void
    {
        $this->fq_type_id = self::TYPE_BASE;
    }

    public function isOriginal(): bool
    {
        return $this->fq_type_id === self::TYPE_ORIGINAL;
    }

    public function original(): void
    {
        $this->fq_type_id = self::TYPE_ORIGINAL;
    }

    public function isAlternative(): bool
    {
        return $this->fq_type_id === self::TYPE_ALTERNATIVE;
    }

    public function alternative(): void
    {
        $this->fq_type_id = self::TYPE_ALTERNATIVE;
    }

    /**
     * @return bool
     */
    public function createdByExpert(): bool
    {
        return $this->fq_created_expert_id ? true : false;
    }

    /**
     * @return string
     */
    public function getEmployeeName(): string
    {
        $createdByExpert = $this->createdByExpert();

        if ($createdByExpert) {
            return $this->fq_created_expert_name;
        }

        return $this->fqCreatedUser->username;
    }

    /**
     * @param ProductQuote $productQuote
     * @return FlightQuote|null
     */
    public static function findByProductQuoteId(ProductQuote $productQuote): ?FlightQuote
    {
        return self::findOne(['fq_product_quote_id' => $productQuote->pq_id]);
    }

    public function serialize(): array
    {
        return (new FlightQuoteSerializer($this))->getData();
    }

    public static function findByProductQuote(int $productQuoteId): ?Quotable
    {
        return self::find()->byProductQuote($productQuoteId)->limit(1)->one();
    }

    public function getId(): int
    {
        return $this->fq_id;
    }

    /**
     * @return float
     */
    public function getServiceFeePercent(): float
    {
        return $this->fq_service_fee_percent ?? 0.00;
    }

    /**
     * @return float
     */
    public function getProcessingFee(): float
    {
        $processingFeeAmount = $this->fqProductQuote->pqProduct->prType->getProcessingFeeAmount();

        $flight = $this->fqFlight;

        return ($flight->fl_adults + $flight->fl_children) * $processingFeeAmount;
    }

    /**
     * @return float
     */
    public function getSystemMarkUp(): float
    {
        $result = 0.00;
        foreach ($this->flightQuotePaxPrices as $paxPrice) {
            $result += $paxPrice->qpp_system_mark_up * $paxPrice->qpp_cnt;
        }

        return $result;
    }

    /**
     * @return float
     */
    public function getAgentMarkUp(): float
    {
        $result = 0.00;
        foreach ($this->flightQuotePaxPrices as $paxPrice) {
            $result += $paxPrice->qpp_agent_mark_up * $paxPrice->qpp_cnt;
        }
        return $result;
    }

    public static function getGdsList(): array
    {
        return SearchService::GDS_LIST;
    }

    public function isBooked(): bool
    {
        return $this->fqProductQuote->isBooked();
    }

    public function isBookable(): bool
    {
        return (ProductQuoteStatus::isBookable($this->fqProductQuote->pq_status_id) && !$this->isBooked());
    }
}
