<?php

namespace modules\flight\models;

use common\models\Product;
use modules\flight\models\query\FlightQuery;
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
}
