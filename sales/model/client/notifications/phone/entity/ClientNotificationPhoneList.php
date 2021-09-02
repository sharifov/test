<?php

namespace sales\model\client\notifications\phone\entity;

use common\models\ClientPhone;
use sales\model\client\notifications\client\entity\ClientNotification;
use sales\model\client\notifications\client\entity\CommunicationType;
use sales\model\phoneList\entity\PhoneList;

/**
 * This is the model class for table "{{%client_notification_phone_list}}".
 *
 * @property int $cnfl_id
 * @property int|null $cnfl_status_id
 * @property int|null $cnfl_from_phone_id
 * @property int|null $cnfl_to_client_phone_id
 * @property string $cnfl_start
 * @property string $cnfl_end
 * @property int $cnfl_from_hours
 * @property int $cnfl_to_hours
 * @property string|null $cnfl_message
 * @property string|null $cnfl_file_url
 * @property array|null $cnfl_data_json
 * @property string|null $cnfl_call_sid
 * @property string|null $cnfl_created_dt
 * @property string|null $cnfl_updated_dt
 *
 * @property PhoneList $fromPhone
 * @property ClientPhone $clientPhone
 * @property ClientNotification $clientNotification
 *
 * @property Data|null $data
 */
class ClientNotificationPhoneList extends \yii\db\ActiveRecord
{
    private ?Data $data = null;

    public static function create(
        int $fromPhoneId,
        int $toClientPhoneId,
        \DateTimeImmutable $startDt,
        \DateTimeImmutable $endDt,
        int $fromHours,
        int $toHours,
        ?string $message,
        ?string $fileUrl,
        Data $data,
        \DateTimeImmutable $createdDt
    ): self {
        $notification = new self();
        $notification->cnfl_status_id = Status::NEW;
        $notification->cnfl_from_phone_id = $fromPhoneId;
        $notification->cnfl_to_client_phone_id = $toClientPhoneId;
        $notification->cnfl_start = $startDt->format('Y-m-d H:i:s');
        $notification->cnfl_end = $endDt->format('Y-m-d H:i:s');
        $notification->cnfl_from_hours = $fromHours;
        $notification->cnfl_to_hours = $toHours;
        $notification->cnfl_message = $message;
        $notification->cnfl_file_url = $fileUrl;
        $notification->setData($data);
        $notification->cnfl_created_dt = $createdDt->format('Y-m-d H:i:s');
        return $notification;
    }

    public function getData(): Data
    {
        if ($this->data !== null) {
            return $this->data;
        }
        $this->data = Data::createFromArray($this->cnfl_data_json ?: []);
        return $this->data;
    }

    private function setData(Data $data): void
    {
        $this->cnfl_data_json = $data->toArray();
        $this->data = $data;
    }

    public function isNew(): bool
    {
        return $this->cnfl_status_id === Status::NEW;
    }

    public function processing(\DateTimeImmutable $date): void
    {
        $this->cnfl_status_id = Status::PROCESSING;
        $this->cnfl_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function done(string $callSid, \DateTimeImmutable $date): void
    {
        $this->cnfl_call_sid = $callSid;
        $this->cnfl_status_id = Status::DONE;
        $this->cnfl_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function error(\DateTimeImmutable $date): void
    {
        $this->cnfl_status_id = Status::ERROR;
        $this->cnfl_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function cancel(\DateTimeImmutable $date): void
    {
        $this->cnfl_status_id = Status::CANCELED;
        $this->cnfl_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function rules(): array
    {
        return [
            ['cnfl_call_sid', 'default', 'value' => null],
            ['cnfl_call_sid', 'string', 'max' => 34],

            ['cnfl_data_json', 'default', 'value' => null],
            ['cnfl_data_json', 'safe'],

            ['cnfl_start', 'required'],
            ['cnfl_start', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['cnfl_end', 'required'],
            ['cnfl_end', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['cnfl_file_url', 'default', 'value' => null],
            ['cnfl_file_url', 'string', 'max' => 500],

            ['cnfl_from_phone_id', 'required'],
            ['cnfl_from_phone_id', 'integer'],
            ['cnfl_from_phone_id', 'exist', 'skipOnError' => true, 'targetClass' => PhoneList::class, 'targetAttribute' => ['cnfl_from_phone_id' => 'pl_id']],

            ['cnfl_message', 'default', 'value' => null],
            ['cnfl_message', 'string'],

            ['cnfl_status_id', 'required'],
            ['cnfl_status_id', 'integer'],
            ['cnfl_status_id', 'in', 'range' => array_keys(Status::getList())],

            ['cnfl_to_client_phone_id', 'required'],
            ['cnfl_to_client_phone_id', 'integer'],
            ['cnfl_to_client_phone_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientPhone::class, 'targetAttribute' => ['cnfl_to_client_phone_id' => 'id']],

            ['cnfl_to_hours', 'required'],
            ['cnfl_to_hours', 'integer', 'min' => 0, 'max' => 23],

            ['cnfl_from_hours', 'required'],
            ['cnfl_from_hours', 'integer', 'min' => 0, 'max' => 23],
        ];
    }

    public function getFromPhone(): \yii\db\ActiveQuery
    {
        return $this->hasOne(PhoneList::class, ['pl_id' => 'cnfl_from_phone_id']);
    }

    public function getClientPhone(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientPhone::class, ['id' => 'cnfl_to_client_phone_id']);
    }

    public function getClientNotification(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientNotification::class, ['cn_communication_object_id' => 'cnfl_id'])->andWhere(['cn_communication_type_id' => CommunicationType::PHONE]);
    }

    public function attributeLabels(): array
    {
        return [
            'cnfl_id' => 'ID',
            'cnfl_status_id' => 'Status',
            'cnfl_from_phone_id' => 'From Phone ID',
            'cnfl_to_client_phone_id' => 'To Client Phone ID',
            'cnfl_start' => 'Start',
            'cnfl_end' => 'End',
            'cnfl_from_hours' => 'From hours',
            'cnfl_to_hours' => 'To hours',
            'cnfl_message' => 'Message',
            'cnfl_file_url' => 'File Url',
            'cnfl_data_json' => 'DataJson',
            'cnfl_call_sid' => 'Call Sid',
            'cnfl_created_dt' => 'Created Dt',
            'cnfl_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%client_notification_phone_list}}';
    }
}
