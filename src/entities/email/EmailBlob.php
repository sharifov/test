<?php

namespace src\entities\email;

use src\model\BaseActiveRecord;

/**
 * This is the model class for table "email_blob".
 *
 * @property int $embb_id
 * @property int $embb_body_id
 * @property string|null $embb_email_body_blob
 *
 * @property EmailBody $emailBody
 */
class EmailBlob extends BaseActiveRecord
{
    public function rules(): array
    {
        return [
            ['embb_body_id', 'required'],
            ['embb_body_id', 'integer'],

            ['embb_email_body_blob', 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'embb_id' => 'ID',
            'embb_body_id' => 'Email Body ID',
            'embb_email_body_blob' => 'Email Body Blob',
        ];
    }

    public static function tableName(): string
    {
        return 'email_blob';
    }


    public function getEmailBody(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailBody::class, ['embd_id' => 'embb_body_id']);
    }
}
