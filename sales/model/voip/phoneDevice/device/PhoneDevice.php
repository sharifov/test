<?php

namespace sales\model\voip\phoneDevice\device;

use common\models\Employee;
use common\models\UserConnection;
use sales\model\voip\phoneDevice\log\PhoneDeviceLog;

/**
 * This is the model class for table "{{%phone_device}}".
 *
 * @property int $pd_id
 * @property string $pd_hash
 * @property int $pd_user_id
 * @property int|null $pd_connection_id
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
    public static function create(
        int $userId,
        string $hash,
        string $name,
        string $identity,
        bool $statusDevice,
        bool $statusSpeaker,
        bool $statusMicrophone,
        ?string $ip,
        string $created,
        string $updated
    ): self {
        $device = new self();
        $device->pd_user_id = $userId;
        $device->pd_hash = $hash;
        $device->pd_name = $name;
        $device->pd_device_identity = $identity;
        $device->pd_status_device = $statusDevice;
        $device->pd_status_speaker = $statusSpeaker;
        $device->pd_status_microphone = $statusMicrophone;
        $device->pd_ip_address = $ip;
        $device->pd_created_dt = $created;
        $device->pd_updated_dt = $updated;
        return $device;
    }

    public function updateConnectionId(int $connectionId, string $updated): void
    {
        $this->pd_connection_id = $connectionId;
        $this->pd_updated_dt = $updated;
    }

    public function isEqualConnection(int $connectionId): bool
    {
        return $this->pd_connection_id === $connectionId;
    }

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

            ['pd_connection_id', 'integer'],
            ['pd_connection_id', 'exist', 'skipOnError' => true, 'targetClass' => UserConnection::class, 'targetAttribute' => ['pd_connection_id' => 'uc_id']],
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
            'pd_connection_id' => 'Connection ID',
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
