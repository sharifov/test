<?php

namespace sales\model\clientChat\componentRule\entity;

use common\models\Employee;
use sales\model\clientChat\componentEvent\entity\ClientChatComponentEvent;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat_component_rule".
 *
 * @property int $cccr_component_event_id
 * @property string $cccr_value
 * @property int $cccr_runnable_component
 * @property string|null $cccr_component_config
 * @property int|null $cccr_sort_order
 * @property int|null $cccr_enabled
 * @property int|null $cccr_created_user_id
 * @property int|null $cccr_updated_user_id
 * @property string|null $cccr_created_dt
 * @property string|null $cccr_updated_dt
 *
 * @property ClientChatComponentEvent $componentEvent
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class ClientChatComponentRule extends \yii\db\ActiveRecord
{
    private const RUNNABLE_COMPONENT = 1;

    public function rules(): array
    {
        return [
            [['cccr_component_event_id', 'cccr_value', 'cccr_runnable_component'], 'unique', 'targetAttribute' => ['cccr_component_event_id', 'cccr_value', 'cccr_runnable_component']],

            ['cccr_component_config', 'safe'],

            ['cccr_component_event_id', 'required'],
            ['cccr_component_event_id', 'integer'],
            ['cccr_component_event_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChatComponentEvent::class, 'targetAttribute' => ['cccr_component_event_id' => 'ccce_id']],

            ['cccr_created_dt', 'safe'],

            ['cccr_created_user_id', 'integer'],
            ['cccr_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cccr_created_user_id' => 'id']],

            ['cccr_enabled', 'integer'],

            ['cccr_runnable_component', 'required'],
            ['cccr_runnable_component', 'in', 'range' => array_keys(RunnableComponent::getListName())],

            ['cccr_sort_order', 'integer'],

            ['cccr_updated_dt', 'safe'],

            ['cccr_updated_user_id', 'integer'],
            ['cccr_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cccr_updated_user_id' => 'id']],

            ['cccr_value', 'required'],
            ['cccr_value', 'string', 'max' => 10],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cccr_created_dt', 'cccr_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cccr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'cccr_created_user_id',
                'updatedByAttribute' => 'cccr_updated_user_id',
            ],
        ];
    }

    public function getComponentEvent(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChatComponentEvent::class, ['ccce_id' => 'cccr_component_event_id']);
    }

    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cccr_created_user_id']);
    }

    public function getUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cccr_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cccr_component_event_id' => 'Component Event ID',
            'cccr_value' => 'Value',
            'cccr_runnable_component' => 'Runnable Component',
            'cccr_component_config' => 'Component Config',
            'cccr_sort_order' => 'Sort Order',
            'cccr_enabled' => 'Enabled',
            'cccr_created_user_id' => 'Created User ID',
            'cccr_updated_user_id' => 'Updated User ID',
            'cccr_created_dt' => 'Created Dt',
            'cccr_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_component_rule';
    }

    public function getComponentName(): string
    {
        return RunnableComponent::getListName()[$this->cccr_runnable_component] ?? 'Unknown component name';
    }
}
