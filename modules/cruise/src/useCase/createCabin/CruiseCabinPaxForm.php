<?php

namespace modules\cruise\src\useCase\createCabin;

use DateTime;
use modules\cruise\src\entity\cruiseCabin\CruiseCabin;
use modules\cruise\src\entity\cruiseCabinPax\CruiseCabinPax;

class CruiseCabinPaxForm extends CruiseCabinPax
{
    public function rules(): array
    {
        return [
            ['crp_type_id', 'required'],
            ['crp_type_id', 'integer'],

            [['crp_cruise_cabin_id', 'crp_age', 'crp_id'], 'integer'],

            ['crp_dob', 'safe'],

            ['crp_age', 'string', 'max' => 3],
            ['crp_age', 'required', 'when' => static function ($model) {
                return $model->isChild();
            }],
            ['crp_age', 'filter', 'filter' => function () {
                if ($this->crp_dob && $this->crp_age == null) {
                    $dobTime = strtotime($this->crp_dob);
                    $now = time();

                    $age = date('Y', $now) - date('Y', $dobTime);
                    return $age == 0 ? 1 : $age;
                }
                return $this->crp_age;
            }],
            ['crp_age', 'checkDateOfBirth'],


            ['crp_dob', 'filter', 'filter' => static function ($value) {
                return date('Y-m-d', strtotime($value));
            }, 'skipOnEmpty' => true],


            [['crp_first_name', 'crp_last_name'], 'string', 'max' => 40],

            ['crp_id', 'exist', 'skipOnError' => true, 'targetClass' => CruiseCabinPax::class, 'targetAttribute' => ['crp_id' => 'crp_id']],

            ['crp_cruise_cabin_id', 'exist', 'skipOnError' => true, 'targetClass' => CruiseCabin::class, 'targetAttribute' => ['crp_cruise_cabin_id' => 'crc_id']],

            ['crp_dob', 'compareDateOfBirth'],
        ];
    }

    public function checkDateOfBirth(): void
    {
        if ($this->hasErrors()) {
            return;
        }
        $ageRange = $this->getPaxAgeRangeByPaxId($this->crp_type_id);
        if ($this->crp_age !== null && $ageRange && ($this->crp_age < $ageRange['min'] || (isset($ageRange['max']) && $this->crp_age > $ageRange['max']))) {
            $message = 'The age of the ' . $this->getPaxTypeName() . ', should be from ' . $ageRange['min'] . ' ' . (isset($ageRange['max']) ? ' to ' . $ageRange['max'] : '') . ' years old';
            $this->addError('crp_age', $message);
        }

        if ($this->crp_age && $this->crp_dob) {
            $dobTime = new DateTime($this->crp_dob);
            $now = new DateTime();

            $interval = $now->diff($dobTime);

            $age = $interval->y == 0 ? 1 : $interval->y;

            if ($age != $this->crp_age) {
                $this->addError('crp_age', 'Date of birth does not match the entered age.');
            }
        }
    }

    public function compareDateOfBirth(): void
    {
        $today = strtotime('today');
        $dob = strtotime($this->crp_dob);
        if ($today < $dob) {
            $this->addError('crp_dob', 'Date of Birth cant be gather then current date');
        }
    }
}
