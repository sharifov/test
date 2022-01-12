<?php

namespace src\model\lead\useCases\lead\api\create;

use borales\extensions\phoneInput\PhoneInputValidator;
use src\services\client\InternalEmailValidator;
use src\services\client\InternalPhoneValidator;
use thamtech\uuid\validators\UuidValidator;
use yii\base\Model;

/**
 * Class ClientForm
 *
 * @property $phone
 * @property $email
 * @property $uuid
 * @property $chat_visitor_id
 *
 * @property bool|null $allow_contact_internal_phone
 * @property bool|null $allow_contact_internal_email
 */
class ClientForm extends Model
{
    public $phone;
    public $email;
    public $uuid;
    public $chat_visitor_id;

    public $allow_contact_internal_phone;
    public $allow_contact_internal_email;

    public function rules(): array
    {
        return [
            [['allow_contact_internal_phone', 'allow_contact_internal_email'], 'default', 'value' => false],
            [['allow_contact_internal_phone', 'allow_contact_internal_email'], 'boolean'],

            ['phone', 'match', 'pattern' => '/^\+[0-9]+$/', 'message' => 'The format of {attribute} is invalid.'],
            ['phone', PhoneInputValidator::class],
            ['phone', InternalPhoneValidator::class, 'allowInternalPhone' => $this->allow_contact_internal_phone],

            ['email', 'default', 'value' => null],
            ['email', 'string', 'max' => 160],
            ['email', 'email'],
            ['email', 'filter', 'filter' => static function ($value) {
                return $value === null ? null : mb_strtolower(trim($value));
            }],
            ['email', InternalEmailValidator::class, 'allowInternalEmail' => $this->allow_contact_internal_email],

            [['email', 'phone'], 'requiredValidate', 'skipOnEmpty' => false],

            ['uuid', UuidValidator::class, 'skipOnEmpty' => true],

            ['chat_visitor_id', 'string', 'max' => 50],
        ];
    }

    public function requiredValidate(): void
    {
        if (!$this->email && !$this->phone && !$this->chat_visitor_id) {
            $this->addError('phone', 'Phone or Email or Chat_visitor_id required');
            $this->addError('email', 'Email or Phone or Chat_visitor_id required');
            $this->addError('chat_visitor_id', 'Chat_visitor_id or Email or Phone required');
        }
    }
}
