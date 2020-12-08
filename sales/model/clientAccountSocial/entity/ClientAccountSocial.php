<?php

namespace sales\model\clientAccountSocial\entity;

use sales\model\clientAccount\entity\ClientAccount;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "client_account_social".
 *
 * @property int $cas_ca_id
 * @property int $cas_type_id
 * @property string $cas_identity
 * @property string|null $cas_created_dt
 *
 * @property ClientAccount $clientAccount
 */
class ClientAccountSocial extends \yii\db\ActiveRecord
{
    public const TYPE_GOOGLE = 1;
    public const TYPE_FACEBOOK = 2;

    public const TYPE_LIST = [
        self::TYPE_GOOGLE => 'Google',
        self::TYPE_FACEBOOK => 'Facebook',
    ];

    public function rules(): array
    {
        return [
            [['cas_ca_id', 'cas_type_id'], 'unique', 'targetAttribute' => ['cas_ca_id', 'cas_type_id']],

            ['cas_ca_id', 'required'],
            ['cas_ca_id', 'integer'],
            ['cas_ca_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientAccount::class, 'targetAttribute' => ['cas_ca_id' => 'ca_id']],

            ['cas_created_dt', 'safe'],

            ['cas_identity', 'required'],
            ['cas_identity', 'string', 'max' => 255],

            ['cas_type_id', 'required'],
            ['cas_type_id', 'integer'],
            ['cas_type_id', 'in', 'range' => array_keys(self::TYPE_LIST)],
        ];
    }

    public function getClientAccount(): ActiveQuery
    {
        return $this->hasOne(ClientAccount::class, ['ca_id' => 'cas_ca_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cas_ca_id' => 'ClientAccount ID',
            'cas_type_id' => 'Type ID',
            'cas_identity' => 'Identity',
            'cas_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): ClientAccountSocialScopes
    {
        return new ClientAccountSocialScopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%client_account_social}}';
    }
}
