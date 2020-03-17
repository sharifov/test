<?php

namespace modules\flight\models;

use modules\flight\src\entities\flight\events\FlightChangedDelayedChargeEvent;
use modules\flight\src\entities\flight\events\FlightChangedStopsEvent;
use modules\flight\src\entities\flight\serializer\FlightSerializer;
use modules\product\src\entities\product\Product;
use modules\flight\models\query\FlightQuery;
use modules\flight\src\events\FlightRequestUpdateEvent;
use modules\product\src\interfaces\Productable;
use sales\entities\EventTrait;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "flight".
 *
 * @property int $fl_id
 * @property int|null $fl_product_id
 * @property int|null $fl_trip_type_id
 * @property string|null $fl_cabin_class
 * @property int|null $fl_adults
 * @property int|null $fl_children
 * @property int|null $fl_infants
 * @property string|null $fl_request_hash_key
 * @property int|null $fl_stops
 * @property bool|null $fl_delayed_charge
 * *
 * @property Product $flProduct
 * @property FlightPax[] $flightPaxes
 * @property FlightQuote[] $flightQuotes
 * @property string $cabinClassName
 * @property string $tripTypeName
 * @property FlightSegment[] $flightSegments
 */
class Flight extends \yii\db\ActiveRecord implements Productable
{
	use EventTrait;

	public const AGENT_PROCESSING_FEE_PER_PAX = 25.00;

    public const TRIP_TYPE_ONE_WAY           = 1;
    public const TRIP_TYPE_ROUND_TRIP        = 2;
    public const TRIP_TYPE_MULTI_DESTINATION = 3;

    public const TRIP_TYPE_LIST = [
        self::TRIP_TYPE_ROUND_TRIP          => 'Round Trip',
        self::TRIP_TYPE_ONE_WAY             => 'One Way',
        self::TRIP_TYPE_MULTI_DESTINATION   => 'Multi destination'
    ];

    public const CABIN_CLASS_ECONOMY      = 'E';
    public const CABIN_CLASS_BUSINESS     = 'B';
    public const CABIN_CLASS_FIRST        = 'F';
    public const CABIN_CLASS_PREMIUM      = 'P';

    public const CABIN_CLASS_LIST = [
        self::CABIN_CLASS_ECONOMY     => 'Economy',
        self::CABIN_CLASS_PREMIUM     => 'Premium eco',
        self::CABIN_CLASS_BUSINESS    => 'Business',
        self::CABIN_CLASS_FIRST       => 'First',
    ];

	public const CABIN_ECONOMY = 'Y';
	public const CABIN_PREMIUM_ECONOMY = 'S';
	public const CABIN_BUSINESS = 'C';
	public const CABIN_PREMIUM_BUSINESS = 'J';
	public const CABIN_FIRST = 'F';
	public const CABIN_PREMIUM_FIRST = 'P';

    public const CABIN_CLASS_REAL_LIST = [
		self::CABIN_CLASS_ECONOMY => self::CABIN_ECONOMY,
		self::CABIN_CLASS_PREMIUM => self::CABIN_PREMIUM_ECONOMY,
		self::CABIN_CLASS_BUSINESS => self::CABIN_BUSINESS ,
		self::CABIN_CLASS_FIRST => self::CABIN_FIRST,
	];

    public static function create(int $productId): self
    {
        $flight = new static();
        $flight->fl_product_id = $productId;
        return $flight;
    }

    public static function createByApi(
        int $productId,
        ?int $tripTypeId,
        ?string $cabinClass,
        int $adults,
        int $children,
        int $infants
    ): self
    {
        $flight = new static();
        $flight->fl_product_id = $productId;
        $flight->fl_trip_type_id = $tripTypeId;
        $flight->fl_cabin_class = $cabinClass;
        $flight->fl_adults = $adults;
        $flight->fl_children = $children;
        $flight->fl_infants = $infants;
        return $flight;
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'flight';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fl_product_id', 'fl_trip_type_id', 'fl_adults', 'fl_children', 'fl_infants'], 'integer'],
            [['fl_cabin_class'], 'string', 'max' => 1],
            [['fl_request_hash_key'], 'string', 'max' => 32],
            [['fl_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['fl_product_id' => 'pr_id']],

            ['fl_stops', 'default', 'value' => null],
            ['fl_stops', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['fl_stops', 'integer', 'max' => 9],

            ['fl_delayed_charge', 'boolean'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'fl_id' => 'ID',
            'fl_product_id' => 'Product ID',
            'fl_trip_type_id' => 'Trip Type ID',
            'fl_cabin_class' => 'Cabin Class',
            'fl_adults' => 'Adults',
            'fl_children' => 'Children',
            'fl_infants' => 'Infants',
            'fl_request_hash_key' => 'Request Hash key',
            'fl_stops' => 'Stops',
            'fl_delayed_charge' => 'Delayed charge',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getFlProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['pr_id' => 'fl_product_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFlightPaxes(): ActiveQuery
    {
        return $this->hasMany(FlightPax::class, ['fp_flight_id' => 'fl_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFlightQuotes(): ActiveQuery
    {
        return $this->hasMany(FlightQuote::class, ['fq_flight_id' => 'fl_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFlightSegments(): ActiveQuery
    {
        return $this->hasMany(FlightSegment::class, ['fs_flight_id' => 'fl_id']);
    }

    /**
     * {@inheritdoc}
     * @return FlightQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FlightQuery(static::class);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            $request_hash_key = $this->generateRequestHashKey();
            if ($this->fl_request_hash_key !== $request_hash_key) {
                $this->fl_request_hash_key = $request_hash_key;
            }
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    private function generateRequestHashKey(): string
    {
        $keyData[] = $this->fl_cabin_class . '|' . $this->fl_adults . '|' . $this->fl_children . '|' . $this->fl_infants;
        if ($this->flightSegments) {
            foreach ($this->flightSegments as $segment) {
                $keyData[] = $segment->fs_origin_iata . '|' . $segment->fs_destination_iata . '|' . $segment->fs_departure_date;
            }
        }
        $key = implode('|', $keyData);
        return md5($key);
    }

    /**
     * @return array
     */
    public static function getTripTypeList(): array
    {
        return self::TRIP_TYPE_LIST;
    }

    /**
     * @return string
     */
    public function getTripTypeName(): string
    {
        return self::TRIP_TYPE_LIST[$this->fl_trip_type_id] ?? '-';
    }

    /**
     * @return array
     */
    public static function getCabinClassList(): array
    {
        return self::CABIN_CLASS_LIST;
    }

    /**
     * @return string
     */
    public function getCabinClassName(): string
    {
        return self::CABIN_CLASS_LIST[$this->fl_cabin_class] ?? '-';
    }

    /**
     * @param string $cabin
     * @param int $adults
     * @param int $children
     * @param int $infants
     * @param int $stops
     * @param bool $delayedCharge
     */
    public function editItinerary(
        string $cabin,
        int $adults,
        int $children,
        int $infants,
        ?int $stops,
        bool $delayedCharge
    ): void
    {
        if ($this->fl_cabin_class !== $cabin) {
            $this->recordEvent(new FlightRequestUpdateEvent($this), FlightRequestUpdateEvent::EVENT_KEY);
        }
        $this->fl_cabin_class = $cabin;
        $this->editPassengers($adults, $children, $infants);
        $this->updateStops($stops);
        $this->updateDelayedCharge($delayedCharge);
    }

	public function updateStops(?int $value)
    {
        if ($this->fl_stops !== $value) {
            $this->recordEvent(new FlightChangedStopsEvent($this));
        }
        $this->fl_stops = $value;
    }

	public function updateDelayedCharge(bool $value)
    {
        if ($this->fl_delayed_charge !== $value) {
            $this->recordEvent(new FlightChangedDelayedChargeEvent($this));
        }
        $this->fl_delayed_charge = $value;
    }

	/**
	 * @param int $adults
	 * @param int $children
	 * @param int $infants
	 */
	public function editPassengers(int $adults, int $children, int $infants): void
	{
		if ($this->fl_adults !== $adults || $this->fl_children !== $children || $this->fl_infants !== $infants) {
			$this->recordEvent(new FlightRequestUpdateEvent($this), FlightRequestUpdateEvent::EVENT_KEY);
		}
		$this->fl_adults = $adults;
		$this->fl_children = $children;
		$this->fl_infants = $infants;
	}

	/**
	 * @param int|null $type
	 */
	public function setTripType(?int $type = null): void
	{
		$list = self::getTripTypeList();
		if (isset($list[$type])) {
			if ($this->fl_trip_type_id !== $type) {
				$this->recordEvent((new FlightRequestUpdateEvent($this)), FlightRequestUpdateEvent::EVENT_KEY);
			}

			$this->fl_trip_type_id = $type;
		} else {
			$this->fl_trip_type_id = null;
			$this->recordEvent((new FlightRequestUpdateEvent($this)), FlightRequestUpdateEvent::EVENT_KEY);
		}
	}

	/**
	 * @return array
	 */
	public static function getCabinClassRealList(): array
	{
		return self::CABIN_CLASS_REAL_LIST;
	}

	/**
	 * @param string $code
	 * @return mixed|string
	 */
	public function getCabinRealCode(string $code)
	{
		return self::getCabinClassRealList()[$code] ?? '';
	}

	/**
	 * @return int
	 */
	public function updateLastAction() : int
	{
		return 1;
//		return self::updateAll(['l_last_action_dt' => date('Y-m-d H:i:s')], ['id' => $this->id]);
	}

	/**
	 * @param int|null $excludeQuoteId
	 * @return bool
	 */
	public function originalQuoteExist(int $excludeQuoteId = null): bool
	{
		foreach ($this->flightQuotes as $quote) {
			if ($quote->isOriginal()) {
				if ($excludeQuoteId) {
					if ($quote->fq_id !== $excludeQuoteId) {
						return true;
					}
				} else {
					return true;
				}
			}
		}
		return false;
	}

    public function serialize(): array
    {
        return (new FlightSerializer($this))->getData();
	}

    public function getId(): int
    {
        return $this->fl_id;
	}

	public static function findByProduct(int $productId): ?Productable
    {
        return self::find()->byProduct($productId)->limit(1)->one();
    }
}
