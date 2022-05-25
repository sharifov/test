<?php

namespace src\model\validators\phone;

use borales\extensions\phoneInput\PhoneInputValidator;
use src\services\client\InternalPhoneValidator;
use yii\base\Model;

/**
 * Class PhoneForm
 *
 * @property string $phone
 *
 */
class PhoneForm extends Model
{
    public $phone;

    public function rules(): array
    {
        return [
            ['phone', 'string', 'max' => 50],
            ['phone', 'required'],
            ['phone', 'match', 'pattern' => '/^\+[0-9]+$/', 'message' => 'The format of {attribute} is invalid.'],
            ['phone', PhoneInputValidator::class],
            ['phone', InternalPhoneValidator::class, 'allowInternalPhone' => \Yii::$app->params['settings']['allow_contact_internal_phone'] ?? false],
        ];
    }

    public function formName()
    {
        return '';
    }
}
