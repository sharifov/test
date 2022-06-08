<?php

namespace src\entities\email;

use Yii;

/**
 * This is the model class for table "email_body".
 *
 * @property int $embd_id
 * @property string|null $embd_email_subject
 * @property string|null $embd_email_body_text
 * @property string|null $embd_email_data
 * @property string|null $embd_hash
 *
 * @property EmailBlob $emailBlob
 * @property Email $email
 */
class EmailBody extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['embd_email_body_text', 'string'],

            ['embd_email_data', 'safe'],

            ['embd_email_subject', 'string', 'max' => 255],

            ['embd_hash', 'string', 'max' => 32],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'embd_id' => 'ID',
            'embd_email_subject' => 'Email Subject',
            'embd_email_body_text' => 'Email Body Text',
            'embd_email_data' => 'Email Data',
            'embd_hash' => 'Hash',
        ];
    }

    public static function tableName(): string
    {
        return 'email_body';
    }

    public function getEmailBlob(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailBlob::class, ['embb_body_id' => 'embd_id']);
    }

    public function getEmail(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Email::class, ['e_body_id' => 'embd_id']);
    }
}
