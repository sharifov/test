<?php

namespace modules\webEngage\form;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\components\validators\IsArrayValidator;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class WebEngageUserForm
 */
class WebEngageUserForm extends Model
{
    public $userId;
    public $firstName;
    public $lastName;
    public $email;
    public $phone;
    public $attributes;

    public function rules(): array
    {
        return [
             [['userId'], 'required'],
             [['userId'], 'string', 'max' => 100],

             [['firstName'], 'string'],
             [['lastName'], 'string'],

             [['email'], 'string'],
             [['email'], 'email'],

             [['phone'], 'string'],
             //[['phone'], PhoneInputValidator::class],
             [['phone'], 'match', 'pattern' => "/^\+?[1-9]\d{1,14}$/"],

             [['attributes'], IsArrayValidator::class, 'skipOnError' => true, 'skipOnEmpty' => true],
             [['attributes'], 'checkIsAssociative'],
         ];
    }

    public function checkIsAssociative(string $attribute): void
    {
        if (!ArrayHelper::isAssociative($this->attributes)) {
            $this->addError($attribute, 'Attributes must be associative array');
        }
    }

    public function formName(): string
    {
        return '';
    }
}
