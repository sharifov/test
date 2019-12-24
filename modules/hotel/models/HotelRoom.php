<?php

namespace modules\hotel\models;

use Yii;

/**
 * This is the model class for table "hotel_room".
 *
 * @property int $hr_id
 * @property int $hr_hotel_id
 * @property string|null $hr_room_name
 *
 * @property Hotel $hrHotel
 * @property HotelRoomPax[] $hotelRoomPaxes
 */
class HotelRoom extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hotel_room';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hr_hotel_id'], 'required'],
            [['hr_hotel_id'], 'integer'],
            [['hr_room_name'], 'string', 'max' => 200],
            [['hr_hotel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hotel::class, 'targetAttribute' => ['hr_hotel_id' => 'ph_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hr_id' => 'ID',
            'hr_hotel_id' => 'Hotel ID',
            'hr_room_name' => 'Room Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHrHotel()
    {
        return $this->hasOne(Hotel::class, ['ph_id' => 'hr_hotel_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHotelRoomPaxes()
    {
        return $this->hasMany(HotelRoomPax::class, ['hrp_hotel_room_id' => 'hr_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\query\HotelRoomQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\hotel\models\query\HotelRoomQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function getDataSearch(): array
    {
        $data = [];

        $adults = 0;
        $children = 0;
        $paxes = [];

        $data['rooms'] = 1;

        if ($this->hotelRoomPaxes) {
            foreach ($this->hotelRoomPaxes as $pax) {
                if ($pax->isAdult()) {
                    $adults ++;
                } elseif ($pax->isChild()) {
                    $children ++;
                    $paxes[] = ['paxType' => 1, 'age' => $pax->hrp_age];
                }
            }
        }


        if ($adults) {
            $data['adults'] = $adults;
        }

        if ($children) {
            $data['children'] = $children;
        }

        if ($paxes) {
            $data['paxes'] = $paxes;
        }

//        ['rooms' => 1, 'adults' => 2, 'children' => 2, 'paxes' => [
//            ['paxType' => 1, 'age' => 6],
//            ['paxType' => 1, 'age' => 14],
//        ]];
        return $data;
    }
}
