<?php

namespace sales\model\client\notifications\sms\entity;

use common\models\ClientPhone;
use sales\model\client\notifications\client\entity\ClientNotification;
use sales\model\client\notifications\client\entity\CommunicationType;
use sales\model\phoneList\entity\PhoneList;

/**
 * This is the model class for table "{{%client_notification_sms_list}}".
 *
 * @property int $cnsl_id
 * @property int|null $cnsl_status_id
 * @property int|null $cnsl_from_phone_id
 * @property int|null $cnsl_name_from
 * @property int|null $cnsl_to_client_phone_id
 * @property string|null $cnsl_start
 * @property string|null $cnsl_end
 * @property string|null $cnsl_message
 * @property array|null $cnsl_data_json
 * @property string|null $cnsl_sms_sid
 * @property string|null $cnsl_created_dt
 * @property string|null $cnsl_updated_dt
 *
 * @property PhoneList $fromPhone
 * @property ClientPhone $clientPhone
 * @property ClientNotification $clientNotification
 *
 * @property Data|null $data
 */
class ClientNotificationSmsList extends \yii\db\ActiveRecord
{
    private ?Data $data = null;

    public static function create(
        int $fromPhoneId,
        string $nameFrom,
        int $toClientPhoneId,
        ?\DateTimeImmutable $startDt,
        ?\DateTimeImmutable $endDt,
        string $message,
        Data $data,
        \DateTimeImmutable $createdDt
    ): self {
        $notification = new self();
        $notification->cnsl_status_id = Status::NEW;
        $notification->cnsl_from_phone_id = $fromPhoneId;
        $notification->cnsl_name_from = $nameFrom;
        $notification->cnsl_to_client_phone_id = $toClientPhoneId;
        $notification->cnsl_start = $startDt ? $startDt->format('Y-m-f H:i:s') : null;
        $notification->cnsl_end = $endDt ? $endDt->format('Y-m-f H:i:s') : null;
        $notification->cnsl_message = $message;
        $notification->setData($data);
        $notification->cnsl_created_dt = $createdDt->format('Y-m-d H:i:s');
        return $notification;
    }

    public function getData(): Data
    {
        if ($this->data !== null) {
            return $this->data;
        }
        $this->data = Data::createFromArray($this->cnsl_data_json ?: []);
        return $this->data;
    }

    private function setData(Data $data): void
    {
        $this->cnsl_data_json = $data->toArray();
        $this->data = $data;
    }

    public function isNew(): bool
    {
        return $this->cnsl_status_id === Status::NEW;
    }

    public function processing(string $smsSid, \DateTimeImmutable $date): void
    {
        $this->cnsl_status_id = Status::PROCESSING;
        $this->cnsl_sms_sid = $smsSid;
        $this->cnsl_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function done(\DateTimeImmutable $date): void
    {
        $this->cnsl_status_id = Status::DONE;
        $this->cnsl_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function error(\DateTimeImmutable $date): void
    {
        $this->cnsl_status_id = Status::ERROR;
        $this->cnsl_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function cancel(\DateTimeImmutable $date): void
    {
        $this->cnsl_status_id = Status::CANCELED;
        $this->cnsl_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function rules(): array
    {
        return [
            ['cnsl_sms_sid', 'default', 'value' => null],
            ['cnsl_sms_sid', 'string', 'max' => 34],

            ['cnsl_data_json', 'default', 'value' => null],
            ['cnsl_data_json', 'safe'],

            ['cnsl_start', 'default', 'value' => null],
            ['cnsl_start', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['cnsl_end', 'default', 'value' => null],
            ['cnsl_end', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['cnsl_from_phone_id', 'required'],
            ['cnsl_from_phone_id', 'integer'],
            ['cnsl_from_phone_id', 'exist', 'skipOnError' => true, 'targetClass' => PhoneList::class, 'targetAttribute' => ['cnsl_from_phone_id' => 'pl_id']],

            ['cnsl_name_from', 'string'],

            ['cnsl_message', 'default', 'value' => null],
            ['cnsl_message', 'string'],

            ['cnsl_status_id', 'required'],
            ['cnsl_status_id', 'integer'],
            ['cnsl_status_id', 'in', 'range' => array_keys(Status::getList())],

            ['cnsl_to_client_phone_id', 'required'],
            ['cnsl_to_client_phone_id', 'integer'],
            ['cnsl_to_client_phone_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientPhone::class, 'targetAttribute' => ['cnsl_to_client_phone_id' => 'id']],
        ];
    }

    public function getFromPhone(): \yii\db\ActiveQuery
    {
        return $this->hasOne(PhoneList::class, ['pl_id' => 'cnsl_from_phone_id']);
    }

    public function getClientPhone(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientPhone::class, ['id' => 'cnsl_to_client_phone_id']);
    }

    public function getClientNotification(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientNotification::class, ['cn_communication_object_id' => 'cnsl_id'])->andWhere(['cn_communication_type_id' => CommunicationType::SMS]);
    }

    public function attributeLabels(): array
    {
        return [
            'cnsl_id' => 'ID',
            'cnsl_status_id' => 'Status',
            'cnsl_from_phone_id' => 'From Phone ID',
            'cnsl_name_from' => 'Name from',
            'cnsl_to_client_phone_id' => 'To Client Phone ID',
            'cnsl_start' => 'Start',
            'cnsl_end' => 'End',
            'cnsl_message' => 'Message',
            'cnsl_data_json' => 'DataJson',
            'cnsl_sms_sid' => 'Sms Sid',
            'cnsl_created_dt' => 'Created Dt',
            'cnsl_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%client_notification_sms_list}}';
    }
}
