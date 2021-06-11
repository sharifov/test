<?php

namespace sales\model\clientChat\componentEvent\entity;

use common\models\Employee;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "client_chat_component_event".
 *
 * @property int $ccce_id
 * @property int|null $ccce_chat_channel_id
 * @property int $ccce_component
 * @property int $ccce_event_type
 * @property string|null $ccce_component_config
 * @property int|null $ccce_enabled
 * @property int|null $ccce_sort_order
 * @property int|null $ccce_created_user_id
 * @property int|null $ccce_updated_user_id
 * @property string|null $ccce_created_dt
 * @property string|null $ccce_updated_dt
 *
 * @property ClientChatChannel $chatChannel
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class ClientChatComponentEvent extends \yii\db\ActiveRecord
{
    private const COMPONENT_CHECK_FLIZZARD_SUBSCRIPTION = 1;

    private const COMPONENT_EVENT_LIST = [
        self::COMPONENT_CHECK_FLIZZARD_SUBSCRIPTION => 'Check Flizzard Subscription'
    ];

    private const COMPONENT_LIST = [
        self::COMPONENT_CHECK_FLIZZARD_SUBSCRIPTION => 'CheckFlizzardSubscriptionComponent'
    ];

    private const COMPONENT_EVENT_TYPE_BEFORE_CHAT_CREATION = 1;
    private const COMPONENT_EVENT_TYPE_AFTER_CHAT_CREATION = 2;

    private const COMPONENT_EVENT_TYPE_LIST = [
        self::COMPONENT_EVENT_TYPE_BEFORE_CHAT_CREATION => 'Before Chat Creation',
        self::COMPONENT_EVENT_TYPE_AFTER_CHAT_CREATION => 'After Chat Creation'
    ];

    public function rules(): array
    {
        return [
            [['ccce_component', 'ccce_event_type'], 'required'],
            [['ccce_chat_channel_id', 'ccce_component', 'ccce_event_type'], 'unique', 'targetAttribute' => ['ccce_chat_channel_id', 'ccce_component', 'ccce_event_type']],

            ['ccce_chat_channel_id', 'integer'],
            ['ccce_chat_channel_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChatChannel::class, 'targetAttribute' => ['ccce_chat_channel_id' => 'ccc_id']],

            ['ccce_component', 'integer'],
            ['ccce_component', 'in', 'range' => array_keys(self::getComponentEventList())],

            ['ccce_component_config', 'safe'],

            ['ccce_created_dt', 'safe'],

            ['ccce_created_user_id', 'integer'],
            ['ccce_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccce_created_user_id' => 'id']],

            ['ccce_enabled', 'integer'],

            ['ccce_event_type', 'integer'],
            ['ccce_event_type', 'in', 'range' => array_keys(self::getComponentTypeList())],

            ['ccce_sort_order', 'integer'],

            ['ccce_updated_dt', 'safe'],

            ['ccce_updated_user_id', 'integer'],
            ['ccce_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccce_updated_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccce_created_dt', 'ccce_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ccce_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'ccce_created_user_id',
                'updatedByAttribute' => 'ccce_updated_user_id',
            ],
        ];
    }

    public function getChatChannel(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChatChannel::class, ['ccc_id' => 'ccce_chat_channel_id']);
    }

    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccce_created_user_id']);
    }

    public function getUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccce_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ccce_id' => 'ID',
            'ccce_chat_channel_id' => 'Chat Channel ID',
            'ccce_component' => 'Component',
            'ccce_event_type' => 'Event Type',
            'ccce_component_config' => 'Component Config',
            'ccce_enabled' => 'Enabled',
            'ccce_sort_order' => 'Sort Order',
            'ccce_created_user_id' => 'Created User ID',
            'ccce_updated_user_id' => 'Updated User ID',
            'ccce_created_dt' => 'Created Dt',
            'ccce_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_component_event';
    }

    public static function getComponentEventList(): array
    {
        return self::COMPONENT_EVENT_LIST;
    }

    public static function getComponentTypeList(): array
    {
        return self::COMPONENT_EVENT_TYPE_LIST;
    }

    public function getComponentEventName(): string
    {
        return self::getComponentEventList()[$this->ccce_component] ?? 'Unknown component event';
    }

    public function getComponentTypeName(): string
    {
        return self::getComponentTypeList()[$this->ccce_event_type] ?? 'Unknown component type';
    }
}
