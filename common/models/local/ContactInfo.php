<?php

namespace common\models\local;

use Yii;
use yii\base\Model;

class ContactInfo extends Model
{
    public $phone = null;
    public $email = null;
    public $password = null;
    public $smtpHost = null;
    public $smtpPort = null;
    public $encryption = null;
    public $email_no_reply_prefix = 'no-reply';

    public function rules()
    {
        return [
            [['phone','email', 'password', 'smtpHost', 'smtpPort', 'encryption', 'email_no_reply_prefix'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'phone' => 'Phone',
            'email' => 'Email',
            'password' => 'Password',
            'smtpHost' => 'SMTP Host',
            'smtpPort' => 'SMTP Port',
            'encryption' => 'Encryption',
            'email_no_reply_prefix' => 'Email NoReply Prefix'
        ];
    }

    public function getEmailNoReply(): string
    {
        return $this->email_no_reply_prefix;
    }
}
