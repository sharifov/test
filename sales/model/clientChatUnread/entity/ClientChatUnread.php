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
    public static function create(int $chatId, int $count, \DateTimeImmutable $date): self
    {
        $model = new self();
        $model->ccu_cc_id = $chatId;
        $model->ccu_created_dt = $date->format('Y-m-d H:i:s');
        $model->ccu_updated_dt = $date->format('Y-m-d H:i:s');
        $model->ccu_count = $count;
        return $model;
    }

    public ?int $ownerId = null;

    public function increase(\DateTimeImmutable $date): void
    {
        $this->ccu_count = (int) $this->ccu_count + 1;
        $this->ccu_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function resetCounter(): void
    {
        $this->ccu_count = 0;
    }

    public function isOwner(int $userId): bool
    {
        if (!$this->ownerId) {
            return false;
        }

        return (int) $this->ownerId === $userId;
    }

    public function touch(\DateTimeImmutable $date): void
    {
        $this->ccu_updated_dt = $date->format('Y-m-d H:i:s');
    }

    public function rules(): array
    {
        return [
            ['ccu_cc_id', 'unique'],
            ['ccu_cc_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['ccu_cc_id' => 'cch_id']],

            ['ccu_count', 'integer', 'max' => 100000],

            ['ccu_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['ccu_updated_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
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
