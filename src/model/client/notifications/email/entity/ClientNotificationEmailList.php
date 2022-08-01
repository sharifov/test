<?php

namespace src\model\client\notifications\email\entity;

use common\models\ClientEmail;
use common\models\Email;
use src\model\client\notifications\client\entity\ClientNotification;
use src\model\client\notifications\client\entity\CommunicationType;
use src\model\emailList\entity\EmailList;

/**
 * This is the model class for table "{{%client_notification_email_list}}".
 *
 * @property int $cnel_id
 * @property int|null $cnel_status_id
 * @property int|null $cnel_from_email_id
 * @property int|null $cnel_name_from
 * @property int|null $cnel_to_client_email_id
 * @property string $cnel_start
 * @property string $cnel_end
 * @property array|null $cnel_data_json
 * @property int|null $cnel_email_id
 * @property string|null $cnel_created_dt
 * @property string|null $cnel_updated_dt
 *
 * @property EmailList $fromEmail
 * @property ClientEmail $clientEmail
 * @property ClientNotification $clientNotification
 *
 * @property Data|null $data
 */
class ClientNotificationEmailList extends \yii\db\ActiveRecord
{
    private ?Data $data = null;

    public static function create(
        int $fromEmailId,
        string $nameFrom,
        int $toClientEmailId,
        \DateTimeImmutable $startDt,
        \DateTimeImmutable $endDt,
        Data $data,
        \DateTimeImmutable $createdDt
    ): self {
        $notification = new self();
        $notification->cnel_status_id = Status::NEW;
        $notification->cnel_from_email_id = $fromEmailId;
        $notification->cnel_name_from = $nameFrom;
        $notification->cnel_to_client_email_id = $toClientEmailId;
        $notification->cnel_start = $startDt->format('Y-m-d H:i:s');
        $notification->cnel_end = $endDt->format('Y-m-d H:i:s');
        $notification->setData($data);
        $notification->cnel_created_dt = $createdDt->format('Y-m-d H:i:s');
        return $notification;
    }

    public function getData(): Data
    {
        if ($this->data !== null) {
            return $this->data;
        }
        $this->data = Data::createFromArray($this->cnel_data_json ?: []);
        return $this->data;
    }

    private function setData(Data $data): void
    {
        $this->cnel_data_json = $data->toArray();
        $this->data = $data;
    }

    public function isNew(): bool
    {
        return $this->cnel_status_id === Status::NEW;
    }

    public function processing(int $emailId, \DateTimeImmutable $date): void
    {
        $this->cnel_email_id = $emailId;
        $this->cnel_status_id = Status::PROCESSING;
        $this->cnel_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function done(\DateTimeImmutable $date): void
    {
        $this->cnel_status_id = Status::DONE;
        $this->cnel_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function error(\DateTimeImmutable $date): void
    {
        $this->cnel_status_id = Status::ERROR;
        $this->cnel_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function cancel(\DateTimeImmutable $date): void
    {
        $this->cnel_status_id = Status::CANCELED;
        $this->cnel_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function rules(): array
    {
        return [
            ['cnel_email_id', 'integer'],
            ['cnel_email_id', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => Email::class, 'targetAttribute' => ['cnel_email_id' => 's_id']],

            ['cnel_data_json', 'default', 'value' => null],
            ['cnel_data_json', 'safe'],

            ['cnel_start', 'required'],
            ['cnel_start', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['cnel_end', 'required'],
            ['cnel_end', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['cnel_from_email_id', 'required'],
            ['cnel_from_email_id', 'integer'],
            ['cnel_from_email_id', 'exist', 'skipOnError' => true, 'targetClass' => EmailList::class, 'targetAttribute' => ['cnel_from_email_id' => 'pl_id']],

            ['cnel_name_from', 'string'],

            ['cnel_status_id', 'required'],
            ['cnel_status_id', 'integer'],
            ['cnel_status_id', 'in', 'range' => array_keys(Status::getList())],

            ['cnel_to_client_email_id', 'required'],
            ['cnel_to_client_email_id', 'integer'],
            ['cnel_to_client_email_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientEmail::class, 'targetAttribute' => ['cnel_to_client_email_id' => 'id']],
        ];
    }

    public function getFromEmail(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailList::class, ['el_id' => 'cnel_from_email_id']);
    }

    public function getClientEmail(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientEmail::class, ['id' => 'cnel_to_client_email_id']);
    }

    public function getClientNotification(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientNotification::class, ['cn_communication_object_id' => 'cnel_id'])->andWhere(['cn_communication_type_id' => CommunicationType::EMAIL]);
    }

    public function attributeLabels(): array
    {
        return [
            'cnel_id' => 'ID',
            'cnel_status_id' => 'Status',
            'cnel_from_email_id' => 'From Email ID',
            'cnel_name_from' => 'Name from',
            'cnel_to_client_email_id' => 'To Client Email ID',
            'cnel_start' => 'Start',
            'cnel_end' => 'End',
            'cnel_data_json' => 'DataJson',
            'cnel_email_id' => 'Email Id',
            'cnel_created_dt' => 'Created Dt',
            'cnel_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%client_notification_email_list}}';
    }
}
