<?php

namespace sales\model\userModelSetting\entity;

use common\components\validators\CheckJsonValidator;
use common\models\Employee;
use frontend\helpers\JsonHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_model_setting".
 *
 * @property int $ums_id
 * @property int $ums_user_id
 * @property string|null $ums_key
 * @property string|null $ums_name
 * @property string|null $ums_type
 * @property string|null $ums_class
 * @property array|null $ums_settings_json
 * @property array|null $ums_sort_order_json
 * @property int|null $ums_per_page
 * @property int|null $ums_enabled
 * @property string|null $ums_created_dt
 * @property string|null $ums_updated_dt
 *
 * @property Employee $umsUser
 */
class UserModelSetting extends ActiveRecord
{
    public const TYPE_USER = 1;
    public const TYPE_USER_GROUP = 2;
    public const TYPE_PROJECT = 3;

    public const TYPE_LIST = [
        self::TYPE_USER => 'by User',
        self::TYPE_USER_GROUP => 'by User Group',
        self::TYPE_PROJECT => 'by Project',
    ];

    public const DEFAULT_NAME = 'Default';

    private ?array $fields = null;

    public function rules(): array
    {
        return [
            [['ums_user_id', 'ums_class', 'ums_settings_json', 'ums_name'], 'required'],

            [['ums_user_id', 'ums_class', 'ums_name'], 'unique', 'targetAttribute' => ['ums_user_id', 'ums_class', 'ums_name']],

            ['ums_class', 'string', 'max' => 255],

            [['ums_name'], 'default', 'value' => self::DEFAULT_NAME],
            [['ums_name'], 'string', 'max' => 50],

            [['ums_key'], 'string', 'max' => 50],
            [['ums_key'], 'filter', 'filter' => function ($value) {
                if (empty($value)) {
                    $value = $this->getKey();
                }
                return $value;
            }],

            [['ums_type'], 'default', 'value' => self::TYPE_USER],
            [['ums_type'], 'integer'],
            [['ums_type'], 'in', 'range' => array_keys(self::TYPE_LIST)],

            [['ums_per_page'], 'integer'],
            [['ums_per_page'], 'default', 'value' => 30],

            [['ums_enabled'], 'default', 'value' => true],
            [['ums_enabled'], 'boolean'],

            [['ums_settings_json', 'ums_sort_order_json'], CheckJsonValidator::class],
            [['ums_settings_json', 'ums_sort_order_json'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],

            ['ums_user_id', 'integer'],
            ['ums_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ums_user_id' => 'id']],

            [['ums_created_dt', 'ums_updated_dt'], 'date', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ums_created_dt', 'ums_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ums_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'ums_id' => 'ID',
            'ums_user_id' => 'User',
            'ums_name' => 'Name',
            'ums_key' => 'Key',
            'ums_type' => 'Type',
            'ums_class' => 'Class',
            'ums_settings_json' => 'Settings Json',
            'ums_sort_order_json' => 'Sort Order Json',
            'ums_per_page' => 'Per Page',
            'ums_enabled' => 'Enabled',
            'ums_created_dt' => 'Created Dt',
            'ums_updated_dt' => 'Updated Dt',
        ];
    }

    public function getUmsUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ums_user_id']);
    }

    public static function find(): UserModelSettingScopes
    {
        return new UserModelSettingScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'user_model_setting';
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getFields(): array
    {
        return $this->fields ?? ($this->fields = ArrayHelper::getValue($this->ums_settings_json, 'fields', []));
    }

    public function getTypeName(): string
    {
        return self::TYPE_LIST[$this->ums_type] ?? '-';
    }

    public function getKey(): string
    {
        return self::keyGenerator(
            $this->ums_user_id,
            $this->ums_class,
            $this->ums_settings_json,
            $this->ums_name
        );
    }

    public function changeFields(array $fields): UserModelSetting
    {
        $settings = $this->ums_settings_json;
        $settings['fields'] = $fields;
        $this->ums_settings_json = $settings;
        return $this;
    }

    public static function keyGenerator(
        int $userId,
        string $class,
        array $settings,
        string $name = self::DEFAULT_NAME
    ): string {
        return md5(implode(
            '_',
            [
                $class,
                $userId,
                $name,
                JsonHelper::encode($settings)
            ]
        ));
    }

    public static function create(
        int $userId,
        string $class,
        array $settings,
        string $name = self::DEFAULT_NAME
    ): UserModelSetting {
        $model = new self();
        $model->ums_user_id = $userId;
        $model->ums_class = $class;
        $model->ums_settings_json = $settings;
        $model->ums_name = $name;
        return $model;
    }
}
