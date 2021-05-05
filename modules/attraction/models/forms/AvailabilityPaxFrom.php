<?php

namespace modules\attraction\models\forms;

use yii\base\Model;

/**
 * Class AttractionOptionsFrom
 * @package modules\attraction\models\forms
 * @property string $availability_id
 * @property array $pax_quantity
 */

class AvailabilityPaxFrom extends Model
{
    public string $availability_id = '';
    public array $pax_quantity = [];

    public function rules()
    {
        return [
            [['availability_id'], 'required'],
            [['pax_quantity'], 'validatePaxQuantity']
        ];
    }

    public function attributeLabels()
    {
        return [
            'availability_id' => 'Availability ID',
        ];
    }

    public function validatePaxQuantity($attribute, $params)
    {
        $isPricingCategorySelected = false;

        foreach ($this->pax_quantity as $key => $unit) {
            if ($isPricingCategorySelected) {
                continue;
            }
            $isPricingCategorySelected = (int) $unit[key($unit)] > 0;
        }

        if (!$isPricingCategorySelected) {
            foreach ($this->pax_quantity as $key => $unit) {
                if ($key > 0) {
                    continue;
                }
                $this->addError($attribute . '[' . $key . '][' . key($unit) . ']', 'Quantity not selected');
            }
        }
    }
}
