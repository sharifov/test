<?php

namespace webapi\src\forms\billing;

use borales\extensions\phoneInput\PhoneInputValidator;
use sales\traits\FormNameModelTrait;
use yii\validators\EmailValidator;

/**
 * Class BillingInfoForm
 */
class BillingInfoForm extends \yii\base\Model
{
    use FormNameModelTrait;

    public $first_name;
    public $last_name;
    public $middle_name;
    public $address_line1;
    public $address_line2;
    public $country_id;
    public $country;
    public $city;
    public $state;
    public $zip;
    public $company_name;
    public $contact_phone;
    public $contact_email;
    public $contact_name;

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'address_line1', 'city', 'country_id', 'country', 'zip', 'contact_phone', 'contact_email'], 'required'],
            [
                ['first_name', 'middle_name', 'last_name', 'address_line1', 'address_line2', 'company_name',
                'country_id', 'city', 'state', 'zip', 'contact_phone', 'contact_email', 'contact_name', 'country'],
                'string'
            ],
            [['first_name', 'last_name', 'middle_name', 'city'], 'string', 'max' => 30],
            [['address_line1', 'address_line2'], 'string', 'max' => 50],
            [['state', 'company_name'], 'string', 'max' => 40],
            [['contact_name'], 'string', 'max' => 60],
            [['country_id'], 'string', 'max' => 2],
            [['zip'], 'string', 'max' => 10],

            [['contact_phone'], 'string', 'max' => 20],
            [['contact_phone'], PhoneInputValidator::class, 'skipOnEmpty' => true],

            [['contact_email'], 'string', 'max' => 160],
            [['contact_email'], EmailValidator::class, 'skipOnEmpty' => true],
        ];
    }
}
