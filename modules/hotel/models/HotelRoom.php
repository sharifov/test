<?php

namespace modules\hotel\models;

use modules\hotel\models\query\HotelRoomQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "hotel_room".
 *
 * @property int $hr_id
 * @property int $hr_hotel_id
 * @property string|null $hr_room_name
 *
 * @property Hotel $hrHotel
 * @property int $adtCount
 * @property int $chdCount
 * @property array $dataSearch
 * @property HotelRoomPax[] $hotelRoomPaxes
 */
class HotelRoom extends \yii\db\ActiveRecord
{
    private $_adults    = null;
    private $_children  = null;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'hotel_room';
    }

    /**
     * @return array
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
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'hr_id' => 'ID',
            'hr_hotel_id' => 'Hotel ID',
            'hr_room_name' => 'Room Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getHrHotel()
    {
        return $this->hasOne(Hotel::class, ['ph_id' => 'hr_hotel_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHotelRoomPaxes(): ActiveQuery
    {
        return $this->hasMany(HotelRoomPax::class, ['hrp_hotel_room_id' => 'hr_id']);
    }

    /**
     * {@inheritdoc}
     * @return HotelRoomQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new HotelRoomQuery(static::class);
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

        $this->_adults = $adults;
        $this->_children = $children;

//        ['rooms' => 1, 'adults' => 2, 'children' => 2, 'paxes' => [
//            ['paxType' => 1, 'age' => 6],
//            ['paxType' => 1, 'age' => 14],
//        ]];

        return $data;
    }

    /**
     * @return int
     */
    public function getAdtCount(): int
    {
        if ($this->_adults === null) {
            $this->getDataSearch();
        }
        return $this->_adults ?: 0;
    }

    /**
     * @return int
     */
    public function getChdCount(): int
    {
        if ($this->_children === null) {
            $this->getDataSearch();
        }
        return $this->_children ?: 0;
    }
}
