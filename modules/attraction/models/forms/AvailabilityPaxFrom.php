<?php

namespace modules\attraction\models\forms;

use yii\base\Model;

/**
 * Class AttractionOptionsFrom
 * @package modules\attraction\models\forms
 * @property string $availability_id
 */

class AvailabilityPaxFrom extends Model
{
    public string $availability_id = '';
    public array $pax_quantity = [];

    public function rules()
    {
        return [
            [['availability_id'], 'required'],
            [['pax_quantity'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'availability_id' => 'Availability ID',
        ];
    }
}
