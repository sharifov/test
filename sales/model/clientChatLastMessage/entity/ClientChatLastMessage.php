<?php

namespace sales\model\clientChatLastMessage\entity;

use sales\model\clientChat\entity\ClientChat;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat_last_message".
 *
 * @property int $cclm_id
 * @property int|null $cclm_cch_id
 * @property int|null $cclm_type_id 1 - client, 2 - agent, 3 - bot
 * @property string|null $cclm_message
 * @property string|null $cclm_dt
 *
 * @property ClientChat $clientChat
 */
class ClientChatLastMessage extends ActiveRecord
{
    public const TYPE_CLIENT = 1;
    public const TYPE_AGENT = 2;
    public const TYPE_BOT = 3;

    public const TYPE_LIST = [
        self::TYPE_CLIENT => 'Client',
        self::TYPE_AGENT => 'Agent',
        self::TYPE_BOT => 'Bot',
    ];

    public static function tableName(): string
    {
        return '{{%client_chat_last_message}}';
    }

    public function rules(): array
    {
        return [
            [['cclm_cch_id'], 'required'],
            [['cclm_cch_id', 'cclm_type_id'], 'integer'],
            [['cclm_message'], 'string'],
            [['cclm_dt'], 'safe'],
            [['cclm_cch_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['cclm_cch_id' => 'cch_id']],

            [['cclm_type_id'], 'in', 'range' => array_keys(self::TYPE_LIST)],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cclm_id' => 'ID',
            'cclm_cch_id' => 'ClientChat ID',
            'cclm_type_id' => 'Type',
            'cclm_message' => 'Message',
            'cclm_dt' => 'Dt',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cclm_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cclm_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function getClientChat(): ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'cclm_cch_id']);
    }

    /**
     * @return ClientChatLastMessageScopes the active query used by this AR class.
     */
    public static function find()
    {
        return new ClientChatLastMessageScopes(static::class);
    }

    public static function getTypeName(int $typeId): string
    {
        return self::TYPE_LIST[$typeId] ?? '-';
    }

    public static function create(int $cchId, int $typeId, string $message, ?string $dt): self
    {
        $model = new static();
        $model->cclm_cch_id = $cchId;
        $model->cclm_type_id = $typeId;
        $model->cclm_message = $message;
        $model->cclm_dt = $dt;
        return $model;
    }
}
