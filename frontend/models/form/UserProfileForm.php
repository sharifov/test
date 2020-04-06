<?php

namespace frontend\models\form;

use yii\base\Model;

/**
 *
 */
class UserProfileForm extends Model
{
    public $email;
    public $password;
    public $full_name;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email', 'password', 'full_name'], 'trim'],
            ['email', 'email'],
            [['password'], 'string', 'min' => 6],
            [['email', 'password', 'full_name'], 'string', 'max' => 255],
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
