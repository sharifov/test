<?php

namespace modules\hotel\models;

use common\models\ProductQuote;
use Yii;

/**
 * This is the model class for table "hotel_quote".
 *
 * @property int $hq_id
 * @property int $hq_hotel_id
 * @property string|null $hq_hash_key
 * @property int|null $hq_product_quote_id
 * @property string|null $hq_json_response
 * @property string|null $hq_destination_name
 * @property string $hq_hotel_name
 * @property int|null $hq_hotel_list_id
 *
 * @property Hotel $hqHotel
 * @property HotelList $hqHotelList
 * @property ProductQuote $hqProductQuote
 * @property HotelQuoteRoom[] $hotelQuoteRooms
 */
class HotelQuote extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hotel_quote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hq_hotel_id', 'hq_hotel_name'], 'required'],
            [['hq_hotel_id', 'hq_product_quote_id', 'hq_hotel_list_id'], 'integer'],
            [['hq_json_response'], 'safe'],
            [['hq_hash_key'], 'string', 'max' => 32],
            [['hq_destination_name'], 'string', 'max' => 255],
            [['hq_hotel_name'], 'string', 'max' => 200],
            [['hq_hash_key'], 'unique'],
            [['hq_hotel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hotel::class, 'targetAttribute' => ['hq_hotel_id' => 'ph_id']],
            [['hq_hotel_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => HotelList::class, 'targetAttribute' => ['hq_hotel_list_id' => 'hl_id']],
            [['hq_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['hq_product_quote_id' => 'pq_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hq_id' => 'ID',
            'hq_hotel_id' => 'Hotel ID',
            'hq_hash_key' => 'Hash Key',
            'hq_product_quote_id' => 'Product Quote ID',
            'hq_json_response' => 'Json Response',
            'hq_destination_name' => 'Destination Name',
            'hq_hotel_name' => 'Hotel Name',
            'hq_hotel_list_id' => 'Hotel List ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHqHotel()
    {
        return $this->hasOne(Hotel::class, ['ph_id' => 'hq_hotel_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHqHotelList()
    {
        return $this->hasOne(HotelList::class, ['hl_id' => 'hq_hotel_list_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHqProductQuote()
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'hq_product_quote_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHotelQuoteRooms()
    {
        return $this->hasMany(HotelQuoteRoom::class, ['hqr_hotel_quote_id' => 'hq_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\query\HotelQuoteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\hotel\models\query\HotelQuoteQuery(get_called_class());
    }
}
