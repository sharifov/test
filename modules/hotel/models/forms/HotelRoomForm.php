<?php

namespace modules\hotel\models\forms;

use modules\hotel\models\Hotel;
use Yii;
use yii\base\Model;

/**
 * This is the model class for table "hotel_room".
 *
 * @property int $hr_id
 * @property int $hr_hotel_id
 * @property string|null $hr_room_name
 *
 */
class HotelRoomForm extends Model
{

    public $hr_id;
    public $hr_hotel_id;
    public $hr_room_name;

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

}
