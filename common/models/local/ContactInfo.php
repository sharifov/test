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
    public $email_from_name = null;

    public function rules()
    {
        return [
            [['phone','email', 'password', 'smtpHost', 'smtpPort', 'encryption', 'email_no_reply_prefix', 'email_from_name'], 'safe'],
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
            'email_no_reply_prefix' => 'Email NoReply Prefix',
            'email_form_name' => 'Email From Name'
        ];
    }

    public function getEmailNoReply(): string
    {
        return $this->email_no_reply_prefix;
    }

    public function getEmailFromName(): ?string
    {
        return $this->email_from_name;
    }
}
