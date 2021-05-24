<?php

namespace webapi\src\forms\leadRequest\userColumnData;

use borales\extensions\phoneInput\PhoneInputValidator;
use sales\services\client\InternalPhoneValidator;
use yii\base\Model;

/**
 * Class UserColumnDataForm
 *
 * @property $email
 * @property $phone
 */
class UserColumnDataForm extends Model
{
    public $email;
    public $phone;

    public function rules(): array
    {
        return [
            [['email', 'phone'], 'checkRequired'],

            [['email'], 'email'],
            [['email'], 'string', 'max' => 100],

            [['phone'], 'string', 'max' => 20],
            ['phone', 'filter', 'filter' => static function ($value) {
                return $value === null ? null : str_replace(['-', ' '], '', trim($value));
            }],
            [['phone'], PhoneInputValidator::class],
            //['phone', InternalPhoneValidator::class, 'allowInternalPhone' => \Yii::$app->params['settings']['allow_contact_internal_phone']], /* TODO::  */
        ];
    }

    public function checkRequired($attribute)
    {
        if (empty($this->email) && empty($this->phone)) {
            $this->addError($attribute, 'Email or Phone number must be filled');
        }
    }
}
