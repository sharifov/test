<?php

namespace src\model\clientData\entity;

use common\models\Client;
use src\model\clientDataKey\entity\ClientDataKey;
use src\traits\FieldsTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "client_data".
 *
 * @property int $cd_id
 * @property int|null $cd_client_id
 * @property int|null $cd_key_id
 * @property string|null $cd_field_value
 * @property string|null $cd_created_dt
 *
 * @property Client $cdClient
 * @property ClientDataKey $cdKey
 */
class ClientData extends \yii\db\ActiveRecord
{
    use FieldsTrait;

    public function rules(): array
    {
        return [
            [['cd_key_id', 'cd_client_id', 'cd_field_value'], 'required'],

//            [['cd_client_id', 'cd_key_id'], 'unique', 'targetAttribute' => ['cd_client_id', 'cd_key_id']],

            ['cd_client_id', 'integer'],
            ['cd_client_id', 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['cd_client_id' => 'id']],

            ['cd_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s', 'skipOnError' => true],

            ['cd_field_value', 'string', 'max' => 500],

            ['cd_key_id', 'integer'],
            ['cd_key_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientDataKey::class, 'targetAttribute' => ['cd_key_id' => 'cdk_id']],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cd_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getCdClient(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'cd_client_id']);
    }

    public function getCdKey(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientDataKey::class, ['cdk_id' => 'cd_key_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cd_id' => 'ID',
            'cd_client_id' => 'Client ID',
            'cd_key_id' => 'Key',
            'cd_field_value' => 'Field Value',
            'cd_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): ClientDataScopes
    {
        return new ClientDataScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_data';
    }

    public static function create(int $clientId, int $keyId, string $value): ClientData
    {
        $model = new self();
        $model->cd_client_id = $clientId;
        $model->cd_key_id = $keyId;
        $model->cd_field_value = $value;
        return $model;
    }
}
