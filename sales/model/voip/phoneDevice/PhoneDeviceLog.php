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
 * @property string $pdl_timestamp_dt
 * @property string $pdl_created_dt
 *
 * @property Employee $user
 */
class PhoneDeviceLog extends \yii\db\ActiveRecord
{
    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pdl_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'pdl_id' => 'ID',
            'pdl_user_id' => 'User',
            'pdl_device_id' => 'Device ID',
            'pdl_level' => 'Level',
            'pdl_message' => 'Message',
            'pdl_error' => 'Error',
            'pdl_stacktrace' => 'Stacktrace',
            'pdl_timestamp_dt' => 'Timestamp',
            'pdl_created_dt' => 'Created',
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
