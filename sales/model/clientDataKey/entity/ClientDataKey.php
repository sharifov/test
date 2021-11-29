<?php

namespace sales\model\clientDataKey\entity;

use common\models\Client;
use common\models\Employee;
use sales\behaviors\cache\CleanCacheBehavior;
use sales\model\clientData\entity\ClientData;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * This is the model class for table "client_data_key".
 *
 * @property int $cdk_id
 * @property string $cdk_key
 * @property string $cdk_name
 * @property string|null $cdk_description
 * @property int|null $cdk_enable
 * @property int|null $cdk_is_system
 * @property string|null $cdk_created_dt
 * @property string|null $cdk_updated_dt
 * @property int|null $cdk_created_user_id
 * @property int|null $cdk_updated_user_id
 *
 * @property Client[] $cdClients
 * @property Employee $cdkCreatedUser
 * @property Employee $cdkUpdatedUser
 * @property ClientData[] $clientData
 */
class ClientDataKey extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['cdk_key', 'required'],
            ['cdk_key', 'string', 'max' => 50],
            ['cdk_key', 'filter', 'filter' => static function ($value) {
                return Inflector::slug($value, '_');
            }],
            ['cdk_key', 'unique'],

            ['cdk_description', 'string', 'max' => 500],

            ['cdk_enable', 'boolean'],
            ['cdk_enable', 'default', 'value' => true],

            ['cdk_is_system', 'boolean'],
            ['cdk_enable', 'default', 'value' => false],

            ['cdk_name', 'required'],
            ['cdk_name', 'string', 'max' => 50],

            [['cdk_updated_user_id', 'cdk_updated_user_id'], 'integer'],
            [['cdk_updated_user_id', 'cdk_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cdk_updated_user_id' => 'id']],

            [['cdk_created_dt', 'cdk_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s', 'skipOnError' => true],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cdk_created_dt', 'cdk_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cdk_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cdk_created_user_id', 'cdk_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cdk_updated_user_id'],
                ],
                'defaultValue' => null,
            ],
            'cleanCache' => [
                'class' => CleanCacheBehavior::class,
                'tags' => [ClientDataKeyDictionary::CACHE_TAG],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getCdClients(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Client::class, ['id' => 'cd_client_id'])->viaTable('client_data', ['cd_key_id' => 'cdk_id']);
    }

    public function getCdkCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cdk_created_user_id']);
    }

    public function getCdkUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cdk_updated_user_id']);
    }

    public function getClientData(): \yii\db\ActiveQuery
    {
        return $this->hasMany(ClientData::class, ['cd_key_id' => 'cdk_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cdk_id' => 'ID',
            'cdk_key' => 'Key',
            'cdk_name' => 'Name',
            'cdk_description' => 'Description',
            'cdk_enable' => 'Enable',
            'cdk_is_system' => 'Is System',
            'cdk_created_dt' => 'Created Dt',
            'cdk_updated_dt' => 'Updated Dt',
            'cdk_created_user_id' => 'Created User',
            'cdk_updated_user_id' => 'Updated User',
        ];
    }

    public static function find(): ClientDataKeyScopes
    {
        return new ClientDataKeyScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_data_key';
    }
}
