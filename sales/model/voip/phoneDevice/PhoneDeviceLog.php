<?php

namespace sales\model\voip\phoneDevice;

use common\models\Employee;

/**
 * This is the model class for table "{{%phone_device_log}}".
 *
 * @property int $pdl_id
 * @property int|null $pdl_user_id
 * @property int|null $pdl_device_id
 * @property int $pdl_level
 * @property string $pdl_message
 * @property string|null $pdl_error
 * @property string|null $pdl_stacktrace
 * @property int $pdl_timestamp_ts
 * @property string $pdl_created_dt
 *
 * @property Employee $pdlUser
 */
class PhoneDeviceLog extends \yii\db\ActiveRecord
{
    public static function create(
        int $userId,
        ?int $deviceId,
        int $level,
        string $message,
        array $error,
        string $stacktrace,
        int $timestamp,
        string $createdDt
    ): self {
        $log = new self();
        $log->pdl_user_id = $userId;
        $log->pdl_device_id = $deviceId;
        $log->pdl_level = $level;
        $log->pdl_message = $message;
        $log->pdl_error = $error;
        $log->pdl_stacktrace = $stacktrace;
        $log->pdl_timestamp_ts = $timestamp;
        $log->pdl_created_dt = $createdDt;
        return $log;
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pdl_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'pdl_id' => 'ID',
            'pdl_user_id' => 'User ID',
            'pdl_device_id' => 'Device ID',
            'pdl_level' => 'Level',
            'pdl_message' => 'Message',
            'pdl_error' => 'Error',
            'pdl_stacktrace' => 'Stacktrace',
            'pdl_timestamp_ts' => 'Timestamp',
            'pdl_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): PhoneDeviceLogScopes
    {
        return new PhoneDeviceLogScopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%phone_device_log}}';
    }
}
