<?php

namespace sales\model\lead\useCases\lead\api\create;

use borales\extensions\phoneInput\PhoneInputValidator;
use sales\services\client\InternalPhoneValidator;
use yii\base\Model;

/**
 * Class ClientForm
 *
 * @property string $phone
 */
class ClientForm extends Model
{
    public $phone;

    public function rules(): array
    {
        return [
            ['phone', 'required'],
            ['phone', 'match', 'pattern' => '/^\+[0-9]+$/', 'message' => 'The format of {attribute} is invalid.'],
            ['phone', PhoneInputValidator::class],
            ['phone', InternalPhoneValidator::class, 'allowInternalPhone' => \Yii::$app->params['settings']['allow_contact_internal_phone']],
        ];
    }
}
