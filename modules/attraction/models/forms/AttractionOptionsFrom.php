<?php

namespace modules\attraction\models\forms;

use yii\base\Model;

/**
 * Class AttractionOptionsFrom
 * @package modules\attraction\models\forms
 * @property string $availability_id
 */

class AttractionOptionsFrom extends Model
{
    public string $availability_id;
    public array $selected_options = [];

    public function rules()
    {
        return [
            [['availability_id'], 'required'],
            [['selected_options'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'availability_id' => 'Availability ID',
        ];
    }
}
