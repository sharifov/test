<?php

namespace frontend\widgets\newWebPhone\sms\form;

use yii\helpers\ArrayHelper;

/**
 * Class SmsSendForm
 *
 * @property string $text
 */
class SmsSendForm extends SmsAuthorizationForm
{
    public $text;

    public function rules(): array
    {
        $rules =  [
            ['text', 'required'],
            ['text', 'string', 'max' => 255],
        ];
        return ArrayHelper::merge(parent::rules(), $rules);
    }
}
