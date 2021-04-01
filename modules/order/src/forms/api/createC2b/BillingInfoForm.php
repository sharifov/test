<?php

namespace modules\order\src\forms\api\createC2b;

use borales\extensions\phoneInput\PhoneInputValidator;
use yii\validators\EmailValidator;

class BillingInfoForm extends \yii\base\Model
{
    public $first_name;

    public $last_name;

    public $middle_name;

    public $address;

    public $country_id;

    public $city;

    public $state;

    public $zip;

    public $phone;

    public $email;

    public function rules()
    {
        return [
            [['first_name', 'middle_name', 'last_name', 'address', 'country_id', 'city', 'state', 'zip', 'phone', 'email'], 'string'],
            [['first_name', 'last_name', 'middle_name', 'city'], 'string', 'max' => 30],
            [['address'], 'string', 'max' => 50],
            [['state'], 'string', 'max' => 40],
            [['country_id'], 'string', 'max' => 2],
            [['zip'], 'string', 'max' => 10],

            [['phone'], 'string', 'max' => 20],
            ['phone', PhoneInputValidator::class],

            [['email'], 'string', 'max' => 160],
            [['email'], EmailValidator::class],
        ];
    }

    public function formName(): string
    {
        return 'billingInfo';
    }
}
