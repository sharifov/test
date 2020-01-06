<?php

namespace modules\flight\models;

use common\models\Product;
use Yii;

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
 * @property FlightSegment[] $flightSegments
 */
class Flight extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
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
     * {@inheritdoc}
     */
    public function attributeLabels()
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
     * @return \yii\db\ActiveQuery
     */
    public function getFlProduct()
    {
        return $this->hasOne(Product::class, ['pr_id' => 'fl_product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlightPaxes()
    {
        return $this->hasMany(FlightPax::class, ['fp_flight_id' => 'fl_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlightQuotes()
    {
        return $this->hasMany(FlightQuote::class, ['fq_flight_id' => 'fl_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlightSegments()
    {
        return $this->hasMany(FlightSegment::class, ['fs_flight_id' => 'fl_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\query\FlightQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\flight\models\query\FlightQuery(static::class);
    }
}
