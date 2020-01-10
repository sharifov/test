<?php

namespace modules\hotel\models\forms;

use DateTime;
use modules\hotel\models\HotelRoom;
use modules\hotel\models\HotelRoomPax;

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
class HotelRoomPaxForm extends HotelRoomPax
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
			['hrp_age', 'string', 'max' => 3],
			[['hrp_hotel_room_id', 'hrp_type_id', 'hrp_age', 'hrp_id'], 'integer'],
            [['hrp_dob'], 'safe'],
			[['hrp_age'], 'required', 'when' => static function ($model) {
        		return $model->isChild();
			}],
            //[['hrp_dob'], 'filter', ],
            [['hrp_dob'], 'filter', 'filter' => static function ($value) {
				return date('Y-m-d', strtotime($value));
            }, 'skipOnEmpty' => true],
			[['hrp_age'], 'filter', 'filter' => function () {
        		if ($this->hrp_dob && $this->hrp_age == null) {
					$dobTime = strtotime($this->hrp_dob);
					$now = time();

					$age = date('Y', $now) - date('Y', $dobTime);
					return $age == 0 ? 1 : $age;
				}
        		return $this->hrp_age;
			}],
            [['hrp_first_name', 'hrp_last_name'], 'string', 'max' => 40],
            [['hrp_id'], 'exist', 'skipOnError' => true, 'targetClass' => HotelRoomPax::class, 'targetAttribute' => ['hrp_id' => 'hrp_id']],
            [['hrp_hotel_room_id'], 'exist', 'skipOnError' => true, 'targetClass' => HotelRoom::class, 'targetAttribute' => ['hrp_hotel_room_id' => 'hr_id']],
			[['hrp_dob'], 'compareDateOfBirth'],
			[['hrp_age'], 'checkDateOfBirth']
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
	 * @return void
	 * @throws \Exception
	 */
	public function checkDateOfBirth(): void
	{
		$ageRange = $this->getPaxAgeRangeByPaxId($this->hrp_type_id);
		if ($this->hrp_age !== null && $ageRange && ($this->hrp_age < $ageRange['min'] || (isset($ageRange['max']) && $this->hrp_age > $ageRange['max']))) {

			$message = 'The age of the '.$this->getPaxTypeName().', should be from '.$ageRange['min'].' '. (isset($ageRange['max']) ? ' to ' . $ageRange['max'] : '').' years old';
			$this->addError('hrp_age', $message);
		}

		if ($this->hrp_age && $this->hrp_dob) {
			$dobTime = new DateTime($this->hrp_dob);
			$now = new DateTime();

			$interval = $now->diff($dobTime);

			$age = $interval->y == 0 ? 1 : $interval->y;

			if ($age != $this->hrp_age) {
				$this->addError('hrp_age', 'Date of birth does not match the entered age.');
			}
		}
	}

	/**
	 * @return void
	 */
	public function compareDateOfBirth(): void
	{
		$today = strtotime('today');
		$dob = strtotime($this->hrp_dob);
		if ($today < $dob) {
			$this->addError('hrp_dob', 'Date of Birth cant be gather then current date');
		}
	}
}
