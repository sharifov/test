<?php

namespace modules\eventManager\src\entities;

use common\components\validators\CheckJsonValidator;
use common\components\validators\CronExpressionValidator;
use common\models\Employee;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the modclass for table "event_list".
 *
 * @property int $el_id
 * @property string $el_key
 * @property string|null $el_category
 * @property string|null $el_description
 * @property int $el_enable_type
 * @property bool $el_enable_log
 * @property bool $el_break
 * @property int $el_sort_order
 * @property string|null $el_cron_expression
 * @property string|null $el_condition
 * @property string|null $el_builder_json
 * @property string|null $el_params
 * @property string|null $el_updated_dt
 * @property int|null $el_updated_user_id
 *
 * @property Employee $elUpdatedUser
 * @property EventHandler[] $eventHandlers
 */
class EventList extends ActiveRecord
{
    public const ET_DISABLED                = 0;
    public const ET_ENABLED                 = 1;
    public const ET_DISABLED_CONDITION      = 2;
    public const ET_ENABLED_CONDITION       = 3;

    public const ET_LIST    = [
        self::ET_DISABLED               => 'Disabled',
        self::ET_ENABLED                => 'Enabled',
        self::ET_DISABLED_CONDITION     => 'Disabled condition',
        self::ET_ENABLED_CONDITION      => 'Enabled condition',
    ];

    public const ET_DESC_LIST    = [
        self::ET_DISABLED               => 'Always off (disabled)',
        self::ET_ENABLED                => 'Always on (enabled)',
        self::ET_DISABLED_CONDITION     => 'Turned on. Disabled only when the time matches the cron expression',
        self::ET_ENABLED_CONDITION      => 'Turned off. Enabled only when the time matches the cron expression',
    ];


    public const ET_CLASS_LIST    = [
        self::ET_DISABLED               => 'danger',
        self::ET_ENABLED                => 'success',
        self::ET_DISABLED_CONDITION     => 'default',
        self::ET_ENABLED_CONDITION      => 'warning',
    ];

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'event_list';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['el_key'], 'required'],
            [['el_enable_type', 'el_enable_log', 'el_break', 'el_sort_order', 'el_updated_user_id'], 'integer'],

            ['el_enable_type', 'in', 'range' => array_keys(self::getEnableTypeList())],

            [['el_condition', 'el_params'], 'string'],

            [['el_params'], CheckJsonValidator::class],

            [['el_params'], 'filter', 'filter' => function ($value) {
                try {
                    $data = [];
                    if (is_string($value)) {
                        $data = \yii\helpers\Json::decode($value);
                    }
                    return $data;
                } catch (\Throwable $throwable) {
                    $this->addError('el_params', $throwable->getMessage());
                    return null;
                }
            }],

            [['el_builder_json', 'el_updated_dt'], 'safe'],
            [['el_key'], 'string', 'max' => 500],
            [['el_category', 'el_cron_expression'], 'string', 'max' => 255],
            [['el_description'], 'string', 'max' => 1000],
            [['el_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class,
                'targetAttribute' => ['el_updated_user_id' => 'id']],

            ['el_cron_expression', CronExpressionValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
    }



    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['el_updated_dt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['el_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'el_updated_user_id',
                'updatedByAttribute' => 'el_updated_user_id',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'el_id' => 'ID',
            'el_key' => 'Key',
            'el_category' => 'Category',
            'el_description' => 'Description',
            'el_enable_type' => 'Enable Type',
            'el_enable_log' => 'Enable Log',
            'el_break' => 'Break',
            'el_sort_order' => 'Sort Order',
            'el_cron_expression' => 'Cron Expression',
            'el_condition' => 'Condition',
            'el_builder_json' => 'Builder Json',
            'el_params' => 'Params',
            'el_updated_dt' => 'Updated Dt',
            'el_updated_user_id' => 'Updated User ID',
        ];
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->event->invalidateCache();
    }

    /**
     * @return bool
     */
    public function beforeDelete(): bool
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        Yii::$app->event->invalidateCache();
        return true;
    }

    /**
     * Gets query for [[ElUpdatedUser]].
     *
     * @return ActiveQuery
     */
    public function getElUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'el_updated_user_id']);
    }

    /**
     * Gets query for [[EventHandlers]].
     *
     * @return ActiveQuery
     */
    public function getEventHandlers(): ActiveQuery
    {
        return $this->hasMany(EventHandler::class, ['eh_el_id' => 'el_id']);
    }

    /**
     * {@inheritdoc}
     * @return EventListScopes the active query used by this AR class.
     */
    public static function find(): EventListScopes
    {
        return new EventListScopes(get_called_class());
    }

    /**
     * @return string[]
     */
    public static function getEnableTypeList(): array
    {
        return self::ET_LIST;
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        $data = self::find()->orderBy(['el_id' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'el_id', 'el_key');
    }
}
