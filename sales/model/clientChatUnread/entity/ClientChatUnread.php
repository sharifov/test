<?php

namespace sales\model\clientChatUnread\entity;

use sales\model\clientChat\entity\ClientChat;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%client_chat_unread}}".
 *
 * @property int         $ccu_cc_id
 * @property int|null    $ccu_count
 * @property string|null $ccu_created_dt
 * @property string|null $ccu_updated_dt
 * @property ClientChat  $chat
 * @property int|null    $ownerId
 */
class ClientChatUnread extends \yii\db\ActiveRecord
{
    public ?int $ownerId = null;

    public function increase(): void
    {
        $this->ccu_count = $this->ccu_count ? (int) $this->ccu_count + 1 : 1;
    }

    public function isOwner(int $userId): bool
    {
        if (!$this->ownerId) {
            return false;
        }

        return (int) $this->ownerId === $userId;
    }

    public function rules(): array
    {
        return [
            ['ccu_cc_id', 'unique'],
            ['ccu_cc_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['ccu_cc_id' => 'cch_id']],

            ['ccu_count', 'integer', 'max' => 100000],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccu_created_dt', 'ccu_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ccu_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function getChat(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'ccu_cc_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ccu_cc_id' => 'Chat Id',
            'ccu_count' => 'Count',
            'ccu_created_dt' => 'Created Dt',
            'ccu_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%client_chat_unread}}';
    }
}
