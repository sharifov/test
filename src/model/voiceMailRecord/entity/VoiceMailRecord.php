<?php

namespace src\model\voiceMailRecord\entity;

use common\models\Client;
use common\models\Employee;
use Yii;

/**
 * This is the model class for table "{{%voice_mail_record}}".
 *
 * @property int $vmr_call_id
 * @property string|null $vmr_record_sid
 * @property int|null $vmr_client_id
 * @property int|null $vmr_user_id
 * @property string|null $vmr_created_dt
 * @property int|null $vmr_duration
 * @property int|null $vmr_new
 * @property int|null $vmr_deleted
 *
 * @property Client $client
 * @property Employee $user
 */
class VoiceMailRecord extends \yii\db\ActiveRecord
{
    public function isOwner(int $userId): bool
    {
        return $this->vmr_user_id === $userId;
    }

    public function getRecordingUrl(): string
    {
        return $this->vmr_record_sid ? Yii::$app->comms->getVoiceMailRecordingUrl($this->vmr_call_id) : '';
    }

    public function rules(): array
    {
        return [
            ['vmr_call_id', 'required'],
            ['vmr_call_id', 'integer'],
            ['vmr_call_id', 'unique'],

            ['vmr_client_id', 'integer'],
            ['vmr_client_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['vmr_client_id', 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['vmr_client_id' => 'id']],

            ['vmr_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['vmr_deleted', 'boolean'],

            ['vmr_duration', 'integer'],

            ['vmr_new', 'boolean'],

            ['vmr_record_sid', 'string', 'max' => 34],

            ['vmr_user_id', 'integer'],
            ['vmr_user_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['vmr_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['vmr_user_id' => 'id']],
        ];
    }

    public function getClient(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'vmr_client_id']);
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'vmr_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'vmr_call_id' => 'Call ID',
            'vmr_record_sid' => 'Record Sid',
            'vmr_client_id' => 'Client ID',
            'vmr_user_id' => 'User',
            'vmr_created_dt' => 'Created Dt',
            'vmr_duration' => 'Duration',
            'vmr_new' => 'New',
            'vmr_deleted' => 'Deleted',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%voice_mail_record}}';
    }
}
