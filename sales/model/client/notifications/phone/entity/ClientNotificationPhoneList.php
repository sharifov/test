<?php

namespace sales\model\client\notifications\phone\entity;

use common\models\ClientPhone;
use sales\model\phoneList\entity\PhoneList;

/**
 * This is the model class for table "{{%client_notification_phone_list}}".
 *
 * @property int $cnfl_id
 * @property int|null $cnfl_status_id
 * @property int|null $cnfl_from_phone_id
 * @property int|null $cnfl_to_client_phone_id
 * @property string|null $cnfl_start
 * @property string|null $cnfl_end
 * @property string|null $cnfl_message
 * @property string|null $cnfl_file_url
 * @property string|null $cnfl_data
 * @property string|null $cnfl_call_sid
 * @property string|null $cnfl_created_dt
 * @property string|null $cnfl_updated_dt
 *
 * @property PhoneList $fromPhone
 * @property ClientPhone $clientPhone
 *
 * @property Data|null $data
 */
class ClientNotificationPhoneList extends \yii\db\ActiveRecord
{
    private ?Data $data = null;

    public function getData(): Data
    {
        if ($this->data !== null) {
            return $this->data;
        }
        $this->data = new Data($this->cnfl_data);
        return $this->data;
    }

    private function setData(Data $data): void
    {
        $this->cnfl_data = $data->toJson();
        $this->data = $data;
    }

    public function setDataCaseId(?int $caseId): void
    {
        $data = $this->getData();
        $data->caseId = $caseId;
        $this->setData($data);
    }

    public function rules(): array
    {
        return [
            ['cnfl_call_sid', 'default', 'value' => null],
            ['cnfl_call_sid', 'string', 'max' => 34],

            ['cnfl_data', 'default', 'value' => null],
            ['cnfl_data', 'safe'],

            ['cnfl_start', 'default', 'value' => null],
            ['cnfl_start', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['cnfl_end', 'default', 'value' => null],
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

    public function attributeLabels(): array
    {
        return [
            'cnfl_id' => 'ID',
            'cnfl_status_id' => 'Status',
            'cnfl_from_phone_id' => 'From Phone ID',
            'cnfl_to_client_phone_id' => 'To Client Phone ID',
            'cnfl_start' => 'Start',
            'cnfl_end' => 'End',
            'cnfl_message' => 'Message',
            'cnfl_file_url' => 'File Url',
            'cnfl_data' => 'Data',
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
