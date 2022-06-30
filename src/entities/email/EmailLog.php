<?php

namespace src\entities\email;

use Yii;
use src\model\BaseActiveRecord;

/**
 * This is the model class for table "email_log".
 *
 * @property int $el_id
 * @property int|null $el_email_id
 * @property string|null $el_status_done_dt
 * @property string|null $el_read_dt
 * @property string|null $el_error_message
 * @property string|null $el_message_id
 * @property string|null $el_ref_message_id
 * @property string|null $el_inbox_created_dt
 * @property int|null $el_inbox_email_id
 * @property int|null $el_communication_id
 * @property int|null $el_is_new
 *
 * @property Email $email
 */
class EmailLog extends BaseActiveRecord
{
    public function rules(): array
    {
        return [
            ['el_communication_id', 'integer'],

            ['el_email_id', 'integer'],
            ['el_email_id', 'exist', 'skipOnError' => true, 'targetClass' => Email::class, 'targetAttribute' => ['el_email_id' => 'e_id']],

            ['el_error_message', 'string', 'max' => 500],

            ['el_inbox_created_dt', 'safe'],

            ['el_inbox_email_id', 'integer'],

            ['el_is_new', 'integer'],

            ['el_message_id', 'string', 'max' => 500],

            ['el_read_dt', 'safe'],

            ['el_ref_message_id', 'string'],

            ['el_status_done_dt', 'safe'],
        ];
    }

    public function getEmail(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Email::class, ['e_id' => 'el_email_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'el_id' => 'ID',
            'el_email_id' => 'Email ID',
            'el_is_new' => 'Is New',
            'el_status_done_dt' => 'Status Done Dt',
            'el_read_dt' => 'Read Dt',
            'el_error_message' => 'Error Message',
            'el_message_id' => 'Message ID',
            'el_ref_message_id' => 'Reference Message ID',
            'el_inbox_created_dt' => 'Inbox Created Dt',
            'el_inbox_email_id' => 'Inbox Email ID',
            'el_communication_id' => 'Communication ID',
        ];
    }

    public static function tableName(): string
    {
        return 'email_log';
    }
}
