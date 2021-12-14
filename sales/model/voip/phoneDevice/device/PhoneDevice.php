<?php

namespace sales\model\voip\phoneDevice\device;

use common\models\Employee;
use common\models\UserConnection;
use sales\model\voip\phoneDevice\log\PhoneDeviceLog;

/**
 * This is the model class for table "{{%phone_device}}".
 *
 * @property int $pd_id
 * @property int $pd_user_id
 * @property int|null $pd_connection_id
 * @property string $pd_name
 * @property string $pd_device_identity
 * @property int|null $pd_status_device
 * @property int|null $pd_status_speaker
 * @property int|null $pd_status_microphone
 * @property string|null $pd_ip_address
 * @property string|null $pd_user_agent
 * @property string $pd_created_dt
 * @property string $pd_updated_dt
 *
 * @property Employee $user
 * @property PhoneDeviceLog[] $logs
 */
class PhoneDevice extends \yii\db\ActiveRecord
{
    public static function new(int $connectionId, int $userId, ?string $ip, ?string $userAgent, string $createdDt): self
    {
        $devicePostfix = (new RandomStringGenerator())->generate(10);
        $device = new self();
        $device->pd_connection_id = $connectionId;
        $device->pd_user_id = $userId;
        $device->pd_name = self::generateName($devicePostfix);
        $device->pd_device_identity = PhoneDeviceIdentity::generate($userId, $devicePostfix);
        $device->pd_status_device = false;
        $device->pd_status_speaker = false;
        $device->pd_status_microphone = false;
        $device->pd_ip_address = $ip;
        $device->pd_user_agent = $userAgent;
        $device->pd_created_dt = $createdDt;
        $device->pd_updated_dt = $createdDt;
        return $device;
    }

    public function updateConnection(int $connectionId, string $ip, string $updated): void
    {
        $this->pd_connection_id = $connectionId;
        $this->pd_ip_address = $ip;
        $this->pd_updated_dt = $updated;
    }

    public function isEqualConnection(int $connectionId): bool
    {
        return $this->pd_connection_id === $connectionId;
    }

    public function isEqualUser(int $userId): bool
    {
        return $this->pd_user_id === $userId;
    }

    public function getClientDeviceIdentity(): string
    {
        return PhoneDeviceIdentity::getPrefix() . $this->pd_device_identity;
    }

    public function isReady(): bool
    {
        return $this->pd_connection_id !== null && $this->deviceIsReady() && $this->speakerIsReady() && $this->microphoneIsReady();
    }

    public function deviceIsReady(): bool
    {
        return $this->pd_status_device ? true : false;
    }

    public function deviceReady(string $updated): void
    {
        $this->pd_status_device = true;
        $this->pd_updated_dt = $updated;
    }

    public function deviceNotReady(string $updated): void
    {
        $this->pd_status_device = false;
        $this->pd_updated_dt = $updated;
    }

    public function speakerIsReady(): bool
    {
        return $this->pd_status_speaker ? true : false;
    }

    public function speakerReady(string $updated): void
    {
        $this->pd_status_speaker = true;
        $this->pd_updated_dt = $updated;
    }

    public function speakerNotReady(string $updated): void
    {
        $this->pd_status_speaker = false;
        $this->pd_updated_dt = $updated;
    }

    public function microphoneIsReady(): bool
    {
        return $this->pd_status_microphone ? true : false;
    }

    public function microphoneReady(string $updated): void
    {
        $this->pd_status_microphone = true;
        $this->pd_updated_dt = $updated;
    }

    public function microphoneNotReady(string $updated): void
    {
        $this->pd_status_microphone = false;
        $this->pd_updated_dt = $updated;
    }

    public static function generateName(string $postFix): string
    {
        return 'Device name #' . $postFix;
    }

    public function rules(): array
    {
        return [
            ['pd_device_identity', 'required'],
            ['pd_device_identity', 'string', 'max' => 255],

            ['pd_ip_address', 'string', 'max' => 45],

            ['pd_user_agent', 'string', 'max' => 500],

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
            'pd_user_id' => 'User',
            'pd_connection_id' => 'Connection ID',
            'pd_name' => 'Name',
            'pd_device_identity' => 'Identity',
            'pd_status_device' => 'Status Device',
            'pd_status_speaker' => 'Status Speaker',
            'pd_status_microphone' => 'Status Microphone',
            'pd_ip_address' => 'Ip Address',
            'pd_user_agent' => 'User agent',
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
