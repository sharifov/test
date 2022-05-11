<?php

namespace modules\taskList\src\entities\taskList;

use common\models\Employee;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "task_list".
 *
 * @property int $tl_id
 * @property string $tl_title
 * @property string $tl_object
 * @property string|null $tl_condition
 * @property string|null $tl_condition_json
 * @property string|null $tl_params_json
 * @property string|null $tl_work_start_time_utc
 * @property string|null $tl_work_end_time_utc
 * @property int|null $tl_duration_min
 * @property int $tl_enable_type
 * @property string|null $tl_cron_expression
 * @property int|null $tl_sort_order
 * @property string|null $tl_updated_dt
 * @property int|null $tl_updated_user_id
 *
 * @property Employee $tlUpdatedUser
 */
class TaskList extends ActiveRecord
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
    public static function tableName(): string
    {
        return 'task_list';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['tl_title', 'tl_object', 'tl_enable_type'], 'required'],
            [['tl_condition_json', 'tl_params_json', 'tl_work_start_time_utc',
                'tl_work_end_time_utc', 'tl_updated_dt'], 'safe'],
            [['tl_duration_min', 'tl_enable_type', 'tl_sort_order', 'tl_updated_user_id'], 'integer'],
            [['tl_title', 'tl_object'], 'string', 'max' => 255],
            [['tl_condition'], 'string', 'max' => 1000],
            [['tl_cron_expression'], 'string', 'max' => 100],
            [['tl_updated_user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Employee::class, 'targetAttribute' => ['tl_updated_user_id' => 'id']],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'tl_id' => 'ID',
            'tl_title' => 'Title',
            'tl_object' => 'Object',
            'tl_condition' => 'Condition',
            'tl_condition_json' => 'Condition Json',
            'tl_params_json' => 'Params Json',
            'tl_work_start_time_utc' => 'Work Start Time Utc',
            'tl_work_end_time_utc' => 'Work End Time Utc',
            'tl_duration_min' => 'Duration Min',
            'tl_enable_type' => 'Enable type',
            'tl_cron_expression' => 'Cron Expression',
            'tl_sort_order' => 'Sort Order',
            'tl_updated_dt' => 'Updated Dt',
            'tl_updated_user_id' => 'Updated User ID',
        ];
    }

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['tl_updated_dt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['tl_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['tl_updated_user_id'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['tl_updated_user_id'],
                ],
                'defaultValue' => null,
            ],
        ];
    }

    /**
     * Gets query for [[TlUpdatedUser]].
     *
     * @return ActiveQuery
     */
    public function getTlUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'tl_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find(): Scopes
    {
        return new Scopes(get_called_class());
    }

    /**
     * @return string[]
     */
    public static function getEnableTypeList(): array
    {
        return self::ET_LIST;
    }

    /**
     * @return string[]
     */
    public static function getTypeList(): array
    {
        return self::TYPE_LIST;
    }

    /**
     * @return string
     */
    public function getEnableTypeName(): string
    {
        return self::ET_LIST[$this->tl_enable_type] ?? '';
    }

    /**
     * @return string
     */
    public function getEnableTypeClass(): string
    {
        return self::ET_CLASS_LIST[$this->ff_enable_type] ?? '';
    }

    /**
     * @return string
     */
    public function getEnableTypeLabel(): string
    {
        $class = $this->getEnableTypeClass();
        $name = $this->getEnableTypeName();
        return Html::tag('span', $name, ['class' => $class ? 'label label-' . $class : null]);
    }
}
