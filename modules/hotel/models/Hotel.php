<?php

namespace modules\hotel\models;

use common\models\Product;
use Yii;

/**
 * This is the model class for table "hotel".
 *
 * @property int $ph_id
 * @property int|null $ph_product_id
 * @property string|null $ph_check_in_date
 * @property string|null $ph_check_out_date
 * @property string|null $ph_destination_code
 * @property int|null $ph_min_star_rate
 * @property int|null $ph_max_star_rate
 * @property int|null $ph_max_price_rate
 * @property int|null $ph_min_price_rate
 *
 * @property Product $phProduct
 * @property HotelQuote[] $hotelQuotes
 * @property HotelRoom[] $hotelRooms
 */
class Hotel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hotel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ph_product_id', 'ph_min_star_rate', 'ph_max_star_rate', 'ph_max_price_rate', 'ph_min_price_rate'], 'integer'],
            [['ph_check_in_date', 'ph_check_out_date'], 'safe'],
            [['ph_destination_code'], 'string', 'max' => 10],
            [['ph_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['ph_product_id' => 'pr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ph_id' => 'ID',
            'ph_product_id' => 'Product ID',
            'ph_check_in_date' => 'Check In Date',
            'ph_check_out_date' => 'Check Out Date',
            'ph_destination_code' => 'Destination Code',
            'ph_min_star_rate' => 'Min Star Rate',
            'ph_max_star_rate' => 'Max Star Rate',
            'ph_max_price_rate' => 'Max Price Rate',
            'ph_min_price_rate' => 'Min Price Rate',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhProduct()
    {
        return $this->hasOne(Product::class, ['pr_id' => 'ph_product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHotelQuotes()
    {
        return $this->hasMany(HotelQuote::class, ['hq_hotel_id' => 'ph_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHotelRooms()
    {
        return $this->hasMany(HotelRoom::class, ['hr_hotel_id' => 'ph_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\query\HotelQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\hotel\models\query\HotelQuery(get_called_class());
    }
}
