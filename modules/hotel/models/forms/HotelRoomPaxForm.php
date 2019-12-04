<?php

namespace modules\hotel\models\forms;

use modules\hotel\models\HotelRoom;
use modules\hotel\models\HotelRoomPax;
use Yii;
use yii\base\Model;

/**
 * This is the model form class for table "hotel_room_pax".
 *
 * @property int $hrp_id
 * @property int $hrp_hotel_room_id
 * @property int $hrp_type_id
 * @property int|null $hrp_age
 * @property string|null $hrp_first_name
 * @property string|null $hrp_last_name
 * @property string|null $hrp_dob
 *
 */
class HotelRoomPaxForm extends Model
{

    public $hrp_id;
    public $hrp_hotel_room_id;
    public $hrp_type_id;
    public $hrp_age;
    public $hrp_first_name;
    public $hrp_last_name;
    public $hrp_dob;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hrp_type_id'], 'required'],
            [['hrp_hotel_room_id', 'hrp_type_id', 'hrp_age', 'hrp_id'], 'integer'],
            [['hrp_dob'], 'safe'],
            //[['hrp_dob'], 'filter', ],
            [['hrp_dob'], 'filter', 'filter' => static function ($value) {
                $result = date('Y-m-d', strtotime($value));
                return $result;
            }, 'skipOnEmpty' => true],
            [['hrp_first_name', 'hrp_last_name'], 'string', 'max' => 40],
            [['hrp_id'], 'exist', 'skipOnError' => true, 'targetClass' => HotelRoomPax::class, 'targetAttribute' => ['hrp_id' => 'hrp_id']],
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

}
