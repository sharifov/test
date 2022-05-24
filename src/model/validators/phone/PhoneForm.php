<?php

namespace src\model\validators\phone;

use src\model\validators\phone;

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
    public string $phone;

    public function rules(): array
    {
        return [
            ['phone', 'string', 'max' => 50],
            ['phone', 'required'],
            ['phone', 'match', 'pattern' => '/^\+[0-9]+$/', 'message' => 'The format of {attribute} is invalid.'],
            ['phone', PhoneInputValidator::class],
            ['phone', InternalPhoneValidator::class, 'allowInternalPhone' => $this->allow_contact_internal_phone],
        ];
    }
}
