<?php

namespace modules\cruise\src\useCase\createCabin;

use modules\cruise\src\entity\cruise\Cruise;
use yii\base\Model;

class CreateCabinForm extends Model
{
    public $crc_id;
    public $crc_cruise_id;
    public $crc_name;
    public $crc_pax_list;

    public function rules(): array
    {
        return [
            ['crc_cruise_id', 'required'],
            ['crc_cruise_id', 'integer'],
            [
                'crc_cruise_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => Cruise::class,
                'targetAttribute' => ['crc_cruise_id' => 'crs_id']
            ],

            ['crc_name', 'string', 'max' => 200],

            ['crc_pax_list', 'validatePaxList'],
        ];
    }

    public function validatePaxList($attribute, $params): void
    {
        if (empty($this->crc_pax_list)) {
            $this->addError('crc_pax_list', 'Pax list cannot be empty');
        } elseif (!is_array($this->crc_pax_list)) {
            $this->addError('crc_pax_list', 'Pax list must be array');
        } else {
            foreach ($this->crc_pax_list as $nr => $paxData) {
                $model = new CruiseCabinPaxForm();
                $model->attributes = $paxData;
                if (!$model->validate()) {
                    if ($model->errors) {
                        foreach ($model->errors as $keyError => $error) {
                            $errorValue = $error[0];
                            $key = $attribute . '[' . $nr . '][' . $keyError . ']';
                            $this->addError($key, 'Pax ' . ($nr + 1) . ': ' . $errorValue);
                        }
                    }
                }
            }
        }
    }

    public function attributeLabels(): array
    {
        return [
            'crc_id' => 'ID',
            'crc_cruise_id' => 'Cruise ID',
            'crc_name' => 'Cabin Name',
            'crc_pax_list' => 'Pax list'
        ];
    }
}
