<?php

namespace sales\model\voip\phoneDevice\device;

use common\models\Employee;
use sales\model\voip\phoneDevice\log\PhoneDeviceLog;

/**
 * This is the model class for table "{{%phone_device}}".
 *
 * @property int $pd_id
 * @property string $pd_hash
 * @property int $pd_user_id
 * @property string $pd_name
 * @property string $pd_device_identity
 * @property int|null $pd_status_device
 * @property int|null $pd_status_speaker
 * @property int|null $pd_status_microphone
 * @property string|null $pd_ip_address
 * @property string $pd_created_dt
 * @property string $pd_updated_dt
 *
 * @property Employee $user
 * @property PhoneDeviceLog[] $logs
 */
class PhoneDevice extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['pd_device_identity', 'required'],
            ['pd_device_identity', 'string', 'max' => 255],

            ['pd_hash', 'required'],
            ['pd_hash', 'string', 'max' => 255],

            ['pd_ip_address', 'string', 'max' => 45],

            ['pd_name', 'required'],
            ['pd_name', 'string', 'max' => 255],

            ['pd_status_device', 'integer'],

            ['pd_status_microphone', 'integer'],

            ['pd_status_speaker', 'integer'],

            ['pd_user_id', 'required'],
            ['pd_user_id', 'integer'],
            ['pd_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pd_user_id' => 'id']],
        ];
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pd_user_id']);
    }

    public function getLogs(): \yii\db\ActiveQuery
    {
        return $this->hasMany(PhoneDeviceLog::class, ['pdl_device_id' => 'pd_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'pd_id' => 'ID',
            'pd_hash' => 'Hash',
            'pd_user_id' => 'User',
            'pd_name' => 'Name',
            'pd_device_identity' => 'Identity',
            'pd_status_device' => 'Status Device',
            'pd_status_speaker' => 'Status Speaker',
            'pd_status_microphone' => 'Status Microphone',
            'pd_ip_address' => 'Ip Address',
            'pd_created_dt' => 'Created',
            'pd_updated_dt' => 'Updated',
        ];
    }

    public static function find(): PhoneDeviceScopes
    {
        return new PhoneDeviceScopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%phone_device}}';
    }
}
