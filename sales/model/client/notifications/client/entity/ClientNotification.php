<?php

namespace sales\model\client\notifications\client\entity;

use common\models\Client;

/**
 * This is the model class for table "client_notification".
 *
 * @property int $cn_id
 * @property int|null $cn_client_id
 * @property int|null $cn_notification_type_id
 * @property int|null $cn_object_id
 * @property int|null $cn_communication_type_id
 * @property int|null $cn_communication_object_id
 * @property string $cn_created_dt
 * @property string $cn_updated_dt
 *
 * @property Client $client
 */
class ClientNotification extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['cn_client_id', 'required'],
            ['cn_client_id', 'integer'],
            ['cn_client_id', 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['cn_client_id' => 'id']],

            ['cn_communication_type_id', 'required'],
            ['cn_communication_type_id', 'integer'],
            ['cn_communication_type_id', 'in', 'range' => array_keys(CommunicationType::getList())],

            ['cn_communication_object_id', 'required'],
            ['cn_communication_object_id', 'integer'],

            ['cn_notification_type_id', 'required'],
            ['cn_notification_type_id', 'integer'],
            ['cn_notification_type_id', 'in', 'range' => array_keys(NotificationType::getList())],

            ['cn_object_id', 'required'],
            ['cn_object_id', 'integer'],
        ];
    }

    public function getClient(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'cn_client_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cn_id' => 'ID',
            'cn_client_id' => 'Client ID',
            'cn_notification_type_id' => 'Notification Type',
            'cn_object_id' => 'Object ID',
            'cn_communication_type_id' => 'Communication Type',
            'cn_communication_object_id' => 'Communication Object ID',
            'cn_created_dt' => 'Created Dt',
            'cn_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%client_notification}}';
    }
}
