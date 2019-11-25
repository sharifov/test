<?php

namespace modules\hotel\models;

use Yii;

/**
 * This is the model class for table "hotel_room_pax".
 *
 * @property int $hrp_id
 * @property int $hrp_hotel_room_id
 * @property int $hrp_type_id
 * @property int|null $hrp_age
 * @property string|null $hrp_first_name
 * @property string|null $hrp_last_name
 * @property string|null $hrp_dob
 *
 * @property HotelRoom $hrpHotelRoom
 */
class HotelRoomPax extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hotel_room_pax';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hrp_hotel_room_id', 'hrp_type_id'], 'required'],
            [['hrp_hotel_room_id', 'hrp_type_id', 'hrp_age'], 'integer'],
            [['hrp_dob'], 'safe'],
            [['hrp_first_name', 'hrp_last_name'], 'string', 'max' => 40],
            [['hrp_hotel_room_id'], 'exist', 'skipOnError' => true, 'targetClass' => HotelRoom::class, 'targetAttribute' => ['hrp_hotel_room_id' => 'hr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hrp_id' => 'ID',
            'hrp_hotel_room_id' => 'Hotel Room ID',
            'hrp_type_id' => 'Type ID',
            'hrp_age' => 'Age',
            'hrp_first_name' => 'First Name',
            'hrp_last_name' => 'Last Name',
            'hrp_dob' => 'DOB',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHrpHotelRoom()
    {
        return $this->hasOne(HotelRoom::class, ['hr_id' => 'hrp_hotel_room_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\query\HotelRoomPaxQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\hotel\models\query\HotelRoomPaxQuery(get_called_class());
    }
}
