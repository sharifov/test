<?php

namespace sales\model\user\entity\userConnectionActiveChat;

use common\models\UserConnection;
use sales\model\clientChat\entity\ClientChat;

/**
 * This is the model class for table "{{%user_connection_active_chat}}".
 *
 * @property int $ucac_conn_id
 * @property int $ucac_chat_id
 *
 * @property ClientChat $chat
 * @property UserConnection $connection
 */
class UserConnectionActiveChat extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['ucac_conn_id', 'ucac_chat_id'], 'unique', 'targetAttribute' => ['ucac_conn_id', 'ucac_chat_id']],

            ['ucac_chat_id', 'required'],
            ['ucac_chat_id', 'integer'],
            ['ucac_chat_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['ucac_chat_id' => 'cch_id']],

            ['ucac_conn_id', 'required'],
            ['ucac_conn_id', 'integer'],
            ['ucac_conn_id', 'exist', 'skipOnError' => true, 'targetClass' => UserConnection::class, 'targetAttribute' => ['ucac_conn_id' => 'uc_id']],
        ];
    }

    public function getChat(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'ucac_chat_id']);
    }

    public function getConnection(): \yii\db\ActiveQuery
    {
        return $this->hasOne(UserConnection::class, ['uc_id' => 'ucac_conn_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ucac_conn_id' => 'Conn ID',
            'ucac_chat_id' => 'Chat ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%user_connection_active_chat}}';
    }
}
