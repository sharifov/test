<?php

namespace modules\featureFlag\src\entities;

use common\models\Employee;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "feature_flag".
 *
 * @property int $ff_id
 * @property string $ff_key
 * @property string|null $ff_name
 * @property string $ff_type
 * @property string $ff_value
 * @property string|null $ff_category
 * @property string|null $ff_description
 * @property int $ff_enable_type
 * @property string|null $ff_attributes
 * @property string|null $ff_condition
 * @property string|null $ff_updated_dt
 * @property int|null $ff_updated_user_id
 *
 * @property Employee $ffUpdatedUser
 */
class FeatureFlag extends ActiveRecord
{
    public const TYPE_BOOL      = 'bool';
    public const TYPE_INT       = 'int';
    public const TYPE_DOUBLE    = 'double';
    public const TYPE_STRING    = 'string';
    public const TYPE_ARRAY     = 'array';
    public const TYPE_OBJECT    = 'object';
    public const TYPE_NULL      = 'null';

    public const TYPE_LIST      = [
        self::TYPE_BOOL     => 'boolean',
        self::TYPE_INT      => 'integer',
        self::TYPE_DOUBLE   => 'double',
        self::TYPE_STRING   => 'string',
        self::TYPE_ARRAY    => 'array',
        self::TYPE_OBJECT   => 'object',
        self::TYPE_NULL     => 'null',
    ];

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

    public const ET_CLASS_LIST    = [
        self::ET_DISABLED               => 'danger',
        self::ET_ENABLED                => 'success',
        self::ET_DISABLED_CONDITION     => 'primary',
        self::ET_ENABLED_CONDITION      => 'warning',
    ];

    public function rules(): array
    {
        return [
            ['ff_attributes', 'safe'],

            ['ff_category', 'string', 'max' => 255],

            ['ff_condition', 'safe'],

            ['ff_description', 'string', 'max' => 1000],

            ['ff_enable_type', 'integer'],

            ['ff_key', 'required'],
            ['ff_key', 'string', 'max' => 255],
            ['ff_key', 'unique'],

            ['ff_name', 'string', 'max' => 255],

            ['ff_type', 'required'],
            ['ff_type', 'string', 'max' => 10],

            ['ff_updated_dt', 'safe'],

            ['ff_updated_user_id', 'integer'],
            ['ff_updated_user_id', 'exist', 'skipOnError' => true,
                'targetClass' => Employee::class, 'targetAttribute' => ['ff_updated_user_id' => 'id']],

            ['ff_value', 'required'],
            ['ff_value', 'string', 'max' => 255],
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
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['ff_updated_dt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['ff_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'ff_updated_user_id',
                'updatedByAttribute' => 'ff_updated_user_id',
            ],
        ];
    }

    public function getFfUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ff_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ff_id' => 'ID',
            'ff_key' => 'Key',
            'ff_name' => 'Name',
            'ff_type' => 'Type',
            'ff_value' => 'Value',
            'ff_category' => 'Category',
            'ff_description' => 'Description',
            'ff_enable_type' => 'Enable Type',
            'ff_attributes' => 'Attributes',
            'ff_condition' => 'Condition',
            'ff_updated_dt' => 'Updated Dt',
            'ff_updated_user_id' => 'Updated User ID',
        ];
    }

    public static function find(): FeatureFlagQuery
    {
        return new FeatureFlagQuery(static::class);
    }

    public static function tableName(): string
    {
        return 'feature_flag';
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
        return self::ET_LIST[$this->ff_enable_type] ?? '';
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
