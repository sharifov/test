<?php

namespace sales\model\lead\useCases\lead\api\create;

use borales\extensions\phoneInput\PhoneInputValidator;
use sales\services\client\InternalEmailValidator;
use sales\services\client\InternalPhoneValidator;
use thamtech\uuid\validators\UuidValidator;
use yii\base\Model;

/**
 * Class ClientForm
 *
 * @property string $phone
 * @property string $email
 * @property string $uuid
 */
class ClientForm extends Model
{
    public $phone;
    public $email;
    public $uuid;

    public function rules(): array
    {
        return [
            ['phone', 'match', 'pattern' => '/^\+[0-9]+$/', 'message' => 'The format of {attribute} is invalid.'],
            ['phone', PhoneInputValidator::class],
            ['phone', InternalPhoneValidator::class, 'allowInternalPhone' => \Yii::$app->params['settings']['allow_contact_internal_phone']],

            ['email', 'default', 'value' => null],
            ['email', 'string', 'max' => 160],
            ['email', 'email'],
            ['email', 'filter', 'filter' => static function($value) {
                return $value === null ? null : mb_strtolower(trim($value));
            }],
            ['email', InternalEmailValidator::class, 'allowInternalEmail' => \Yii::$app->params['settings']['allow_contact_internal_email']],

            [['email', 'phone'], 'requiredValidate', 'skipOnEmpty' => false],

            ['uuid', UuidValidator::class, 'skipOnEmpty' => true],
        ];
    }

    public function requiredValidate(): void
    {
        if (!$this->email && !$this->phone) {
            $this->addError('phone', 'Phone or Email required');
            $this->addError('email', 'Email or Phone required');
        }
    }
}
