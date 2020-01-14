<?php

namespace modules\flight\models;

use common\models\Product;
use modules\flight\models\query\FlightQuery;
use modules\flight\src\events\FlightCountPassengersChangedEvent;
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
 *
 * @property bool $enableActiveRecordEvents
 *
 * @property Product $flProduct
 * @property FlightPax[] $flightPaxes
 * @property FlightQuote[] $flightQuotes
 * @property string $cabinClassName
 * @property string $tripTypeName
 * @property FlightSegment[] $flightSegments
 */
class Flight extends \yii\db\ActiveRecord
{
	use EventTrait;

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

    public $enableActiveRecordEvents = true;

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
	 */
    public function editItinerary(string $cabin, int $adults, int $children, int $infants): void
	{
		$this->fl_cabin_class = $cabin;
		$this->editPassengers($adults, $children, $infants);
	}

	/**
	 * @param int $adults
	 * @param int $children
	 * @param int $infants
	 */
	public function editPassengers(int $adults, int $children, int $infants): void
	{
		if ($this->fl_adults !== $adults || $this->fl_children !== $children || $this->fl_infants !== $infants) {
			$this->recordEvent(new FlightCountPassengersChangedEvent($this));
		}
		$this->fl_adults = $adults;
		$this->fl_children = $children;
		$this->fl_infants = $infants;
	}

	/**
	 * @param string|null $type
	 */
	public function setTripType(string $type = null): void
	{
		if ($type) {
			$list = self::getTripTypeList();
			if (isset($list[$type])) {
				$this->fl_trip_type_id = $type;
				return;
			}
		}
		$this->fl_trip_type_id = null;
	}

	/**
	 * @return void
	 */
	public function disableAREvents(): void
	{
		$this->enableActiveRecordEvents = false;
	}

	/**
	 * @return string
	 */
	public function generateQuoteSearchKeyCache(): string
	{
		$key = 'fl_quote_search_' . $this->fl_id . '-' . $this->fl_adults . '-' . $this->fl_children . '-' . $this->fl_children;
		foreach ($this->flightSegments as $segment) {
			$key .= '-' . $segment->fs_origin_iata . '-' . $segment->fs_destination_iata . '-' . strtotime($segment->fs_departure_date);
		}
		return md5($key);
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
}
