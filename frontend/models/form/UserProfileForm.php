<?php

namespace frontend\models\form;

use kartik\password\StrengthValidator;
use yii\base\Model;

/**
 *
 */
class UserProfileForm extends Model
{
    public $username = null;
    public $email = null;
    public $password = null;
    public $full_name = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['email'], 'required'],
            [['email', 'password', 'full_name'], 'trim'],
            ['email', 'email'],
            ['password', StrengthValidator::class, 'userAttribute' => 'username', 'min' => 10],
            [['email', 'password', 'full_name', 'username'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'email' => 'Email',
            'password' => 'Password',
            'full_name' => 'Full Name',
        ];
    }
}
