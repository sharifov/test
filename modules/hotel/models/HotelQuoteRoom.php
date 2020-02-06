<?php

namespace modules\hotel\models;

use common\models\Currency;
use modules\hotel\models\query\HotelQuoteRoomQuery;
use modules\hotel\src\entities\hotelQuoteRoom\serializer\HotelQuoteRoomSerializer;
use sales\entities\serializer\Serializable;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "hotel_quote_room".
 *
 * @property int $hqr_id
 * @property int $hqr_hotel_quote_id
 * @property string|null $hqr_room_name
 * @property string|null $hqr_key
 * @property string|null $hqr_code
 * @property string|null $hqr_class
 * @property float|null $hqr_amount
 * @property string|null $hqr_currency
 * @property float|null $hqr_cancel_amount
 * @property string|null $hqr_cancel_from_dt
 * @property string|null $hqr_payment_type
 * @property string|null $hqr_board_code
 * @property string|null $hqr_board_name
 * @property int|null $hqr_rooms
 * @property int|null $hqr_adults
 * @property int|null $hqr_children
 *
 * @property Currency $hqrCurrency
 * @property HotelQuote $hqrHotelQuote
 */
class HotelQuoteRoom extends ActiveRecord implements Serializable
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'hotel_quote_room';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['hqr_hotel_quote_id'], 'required'],
            [['hqr_hotel_quote_id', 'hqr_rooms', 'hqr_adults', 'hqr_children'], 'integer'],
            [['hqr_amount', 'hqr_cancel_amount'], 'number'],
            [['hqr_cancel_from_dt'], 'safe'],
            [['hqr_room_name'], 'string', 'max' => 150],
            [['hqr_key'], 'string', 'max' => 255],
            [['hqr_class'], 'string', 'max' => 5],
            [['hqr_currency'], 'string', 'max' => 3],
            [['hqr_payment_type', 'hqr_code'], 'string', 'max' => 10],
            [['hqr_board_code'], 'string', 'max' => 2],
            [['hqr_board_name'], 'string', 'max' => 100],
            [['hqr_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['hqr_currency' => 'cur_code']],
            [['hqr_hotel_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => HotelQuote::class, 'targetAttribute' => ['hqr_hotel_quote_id' => 'hq_id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'hqr_id' => 'ID',
            'hqr_hotel_quote_id' => 'Hotel Quote ID',
            'hqr_room_name' => 'Room Name',
            'hqr_key' => 'Key',
            'hqr_code' => 'Code',
            'hqr_class' => 'Class',
            'hqr_amount' => 'Amount',
            'hqr_currency' => 'Currency',
            'hqr_cancel_amount' => 'Cancel Amount',
            'hqr_cancel_from_dt' => 'Cancel From Dt',
            'hqr_payment_type' => 'Payment Type',
            'hqr_board_code' => 'Board Code',
            'hqr_board_name' => 'Board Name',
            'hqr_rooms' => 'Rooms',
            'hqr_adults' => 'Adults',
            'hqr_children' => 'Children',
        ];
    }


    public function afterFind()
    {
        parent::afterFind();
        $this->hqr_amount                     = $this->hqr_amount === null ? null : (float) $this->hqr_amount;
    }

    /**
     * @return ActiveQuery
     */
    public function getHqrCurrency(): ActiveQuery
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'hqr_currency']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHqrHotelQuote(): ActiveQuery
    {
        return $this->hasOne(HotelQuote::class, ['hq_id' => 'hqr_hotel_quote_id']);
    }

    /**
     * @return HotelQuoteRoomQuery the active query used by this AR class.
     */
    public static function find(): HotelQuoteRoomQuery
    {
        return new HotelQuoteRoomQuery(static::class);
    }

    public function serialize(): array
    {
        return (new HotelQuoteRoomSerializer($this))->getData();
    }
}
