<?php

namespace modules\attraction\models\forms;

use DateTime;
use modules\attraction\models\Attraction;
use modules\attraction\models\AttractionPax;

/**
 * This is the model form class for table "attraction_pax".
 *
 * @property int $atnp_id
 * @property int $atnp_atn_id
 * @property int $atnp_type_id
 * @property int|null $atnp_age
 * @property string|null $atnp_first_name
 * @property string|null $atnp_last_name
 * @property string|null $atnp_dob
 *
 */
class AttractionPaxForm extends AttractionPax
{

    public $atnp_id;
    public $atnp_atn_id;
    public $atnp_type_id;
    public $atnp_age;
    public $atnp_first_name;
    public $atnp_last_name;
    public $atnp_dob;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atnp_type_id'], 'required'],
            ['atnp_age', 'string', 'max' => 3],
            [['atnp_atn_id', 'atnp_type_id', 'atnp_age', 'atnp_id'], 'integer'],
            [['atnp_dob'], 'safe'],
            [['atnp_age'], 'required', 'when' => static function ($model) {
                return $model->isChild();
            }],
            //[['atnp_dob'], 'filter', ],
            [['atnp_dob'], 'filter', 'filter' => static function ($value) {
                return date('Y-m-d', strtotime($value));
            }, 'skipOnEmpty' => true],
            [['atnp_age'], 'filter', 'filter' => function () {
                if ($this->atnp_dob && $this->atnp_age == null) {
                    $dobTime = strtotime($this->atnp_dob);
                    $now = time();

                    $age = date('Y', $now) - date('Y', $dobTime);
                    return $age == 0 ? 1 : $age;
                }
                return $this->atnp_age;
            }],
            [['atnp_first_name', 'atnp_last_name'], 'string', 'max' => 40],
            [['atnp_id'], 'exist', 'skipOnError' => true, 'targetClass' => AttractionPax::class, 'targetAttribute' => ['atnp_id' => 'atnp_id']],
            [['atnp_atn_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attraction::class, 'targetAttribute' => ['atnp_atn_id' => 'atn_id']],
            [['atnp_dob'], 'compareDateOfBirth'],
            [['atnp_age'], 'checkDateOfBirth']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'atnp_id' => 'ID',
            'atnp_atn_id' => 'Hotel Room ID',
            'atnp_type_id' => 'Type ID',
            'atnp_age' => 'Age',
            'atnp_first_name' => 'First Name',
            'atnp_last_name' => 'Last Name',
            'atnp_dob' => 'DOB',
        ];
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function checkDateOfBirth(): void
    {
        $ageRange = $this->getPaxAgeRangeByPaxId($this->atnp_type_id);
        if ($this->atnp_age !== null && $ageRange && ($this->atnp_age < $ageRange['min'] || (isset($ageRange['max']) && $this->atnp_age > $ageRange['max']))) {
            $message = 'The age of the ' . $this->getPaxTypeName() . ', should be from ' . $ageRange['min'] . ' ' . (isset($ageRange['max']) ? ' to ' . $ageRange['max'] : '') . ' years old';
            $this->addError('atnp_age', $message);
        }

        if ($this->atnp_age && $this->atnp_dob) {
            $dobTime = new DateTime($this->atnp_dob);
            $now = new DateTime();

            $interval = $now->diff($dobTime);

            $age = $interval->y == 0 ? 1 : $interval->y;

            if ($age != $this->atnp_age) {
                $this->addError('atnp_age', 'Date of birth does not match the entered age.');
            }
        }
    }

    /**
     * @return void
     */
    public function compareDateOfBirth(): void
    {
        $today = strtotime('today');
        $dob = strtotime($this->atnp_dob);
        if ($today < $dob) {
            $this->addError('atnp_dob', 'Date of Birth cant be gather then current date');
        }
    }
}
