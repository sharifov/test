<?php

namespace modules\hotel\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "hotel_quote_room_pax".
 *
 * @property int $hqrp_id
 * @property int $hqrp_hotel_quote_room_id
 * @property int $hqrp_type_id
 * @property int|null $hqrp_age
 * @property string|null $hqrp_first_name
 * @property string|null $hqrp_last_name
 * @property string|null $hqrp_dob
 *
 * @property HotelQuoteRoom $hqrpHotelRoom
 */
class HotelQuoteRoomPax extends ActiveRecord
{
    public const PAX_TYPE_ADL = 1;
    public const PAX_TYPE_CHD = 2;

    public const PAX_TYPE_LIST = [
        self::PAX_TYPE_ADL => 'Adult',
        self::PAX_TYPE_CHD => 'Child',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hotel_quote_room_pax';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hqrp_hotel_quote_room_id', 'hqrp_type_id'], 'required'],
            [['hqrp_hotel_quote_room_id', 'hqrp_type_id', 'hqrp_age'], 'integer'],
            [['hqrp_dob'], 'safe'],
            [['hqrp_first_name', 'hqrp_last_name'], 'string', 'max' => 40],
            [['hqrp_hotel_quote_room_id'], 'exist', 'skipOnError' => true, 'targetClass' => HotelQuoteRoom::class, 'targetAttribute' => ['hqrp_hotel_quote_room_id' => 'hqr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hqrp_id' => 'ID',
            'hqrp_hotel_quote_room_id' => 'Hotel Room ID',
            'hqrp_type_id' => 'Type ID',
            'hqrp_age' => 'Age',
            'hqrp_first_name' => 'First Name',
            'hqrp_last_name' => 'Last Name',
            'hqrp_dob' => 'Dob',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHqrpHotelRoom()
    {
        return $this->hasOne(HotelQuoteRoom::class, ['hqr_id' => 'hqrp_hotel_quote_room_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\query\HotelQuoteRoomPaxQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\hotel\models\query\HotelQuoteRoomPaxQuery(get_called_class());
    }


}
