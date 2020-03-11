<?php

namespace sales\model\lead\useCases\lead\import;

use borales\extensions\phoneInput\PhoneInputValidator;
use yii\base\Model;

/**
 * Class ClientForm
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 */
class ClientForm extends Model
{
    public $first_name;
    public $last_name;
    public $email;
    public $phone;

    public function rules(): array
    {
        return [
            ['first_name', 'required'],

            [['first_name', 'last_name'], 'string', 'max' => 100],
            [['first_name', 'last_name'], 'match', 'pattern' => "/^[a-z-\s\']+$/i"],
            [['first_name', 'last_name'], 'filter', 'filter' => 'trim'],

            [['email', 'phone'], function () {
                if (!$this->email && !$this->phone) {
                    $this->addError('email', 'Email or Phone cant be null.');
                    $this->addError('phone', 'Phone or Email cant be null.');
                }
            }, 'skipOnEmpty' => false],

            ['email', 'email'],

            ['phone', 'default', 'value' => null],
            ['phone', 'string', 'max' => 100],
            ['phone', PhoneInputValidator::class],
            ['phone', 'filter', 'filter' => static function($value) {
                return $value === null ? null : str_replace(['-', ' '], '', trim($value));
            }],
        ];
    }
}
