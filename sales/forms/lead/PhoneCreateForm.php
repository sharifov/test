<?php

namespace sales\forms\lead;

use yii\base\Model;

/**
 * Class PhoneCreateForm
 * @property string $phone
 * @property string $help - only for View for multiInput Widget
 */
class PhoneCreateForm extends Model
{

    public $phone;
    public $help;

    public function rules(): array
    {
        return [
            ['phone', 'required'],
            ['phone', 'string', 'max' => 100],
            ['phone', 'filter', 'filter' => function($value) {
                return trim($value);
            }]
        ];
    }

}
