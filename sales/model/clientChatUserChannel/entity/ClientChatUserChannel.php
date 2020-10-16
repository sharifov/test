<?php

namespace sales\model\clientChatUserChannel\entity;

use common\models\Employee;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat_user_channel".
 *
 * @property int $ccuc_user_id
 * @property int $ccuc_channel_id
 * @property string|null $ccuc_created_dt
 * @property int|null $ccuc_created_user_id
 *
 * @property ClientChatChannel $ccucChannel
 * @property Employee $ccucCreatedUser
 * @property Employee $ccucUser
 */
class ClientChatUserChannel extends \yii\db\ActiveRecord
{
    public const CACHE_TAG = 'client_chat_user_channel';
    public const CACHE_TAG_USER = self::CACHE_TAG . '-user';
    public const CACHE_DURATION = 60 * 60 * 24;

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccuc_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccuc_created_user_id'],
                ]
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['ccuc_user_id', 'ccuc_channel_id'], 'unique', 'targetAttribute' => ['ccuc_user_id', 'ccuc_channel_id']],

            ['ccuc_channel_id', 'required'],
            ['ccuc_channel_id', 'integer'],
            ['ccuc_channel_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChatChannel::class, 'targetAttribute' => ['ccuc_channel_id' => 'ccc_id']],

            ['ccuc_created_dt', 'safe'],

            ['ccuc_created_user_id', 'integer'],
            ['ccuc_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccuc_created_user_id' => 'id']],

            ['ccuc_user_id', 'required'],
            ['ccuc_user_id', 'integer'],
            ['ccuc_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccuc_user_id' => 'id']],
        ];
    }

    public function getCcucChannel(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChatChannel::class, ['ccc_id' => 'ccuc_channel_id']);
    }

    public function getCcucCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccuc_created_user_id']);
    }

    public function getCcucUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccuc_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ccuc_user_id' => 'User ID',
            'ccuc_channel_id' => 'Channel ID',
            'ccuc_created_dt' => 'Created Dt',
            'ccuc_created_user_id' => 'Created User',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_user_channel';
    }

    public static function cacheTags(int $userId): array
    {
        return [
            self::CACHE_TAG_USER . '-' . $userId,
        ];
    }

    public static function userCacheName(int $userId): string
    {
        return self::CACHE_TAG . '-' . $userId;
    }
}
