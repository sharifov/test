<?php

namespace common\models;

use common\models\query\SettingQuery;
use src\auth\Auth;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\VarDumper;
use yii\helpers\Url;

/**
 * This is the model class for table "setting".
 *
 * @property int $s_id
 * @property string $s_key
 * @property string $s_name
 * @property string $s_description
 * @property string $s_type
 * @property string $s_value
 * @property string $s_updated_dt
 * @property int $s_updated_user_id
 * @property int $s_category_id
 *
 * @property Employee $sUpdatedUser
 * @property SettingCategory $sCategory
 */
class Setting extends \yii\db\ActiveRecord
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

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['s_key', 's_type', 's_value'], 'required'],
            [['s_updated_dt'], 'safe'],
            [['s_updated_user_id', 's_category_id'], 'integer'],
            [['s_key', 's_name'], 'string', 'max' => 700],
            [['s_value'], 'string', 'max' => 5000],
            [['s_type'], 'string', 'max' => 10],
            [['s_description'], 'string', 'max' => 1000],
            [['s_key'], 'unique'],
            [['s_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['s_updated_user_id' => 'id']],
            [['s_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SettingCategory::class, 'targetAttribute' => ['s_category_id' => 'sc_id']],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['s_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['s_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'attribute' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['s_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['s_updated_user_id'],
                ],
                'value' => isset(Yii::$app->user) ? Yii::$app->user->id : null,
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            's_id' => 'ID',
            's_key' => 'Key',
            's_name' => 'Name',
            's_description' => 'Description',
            's_type' => 'Type',
            's_value' => 'Value',
            's_updated_dt' => 'Updated Dt',
            's_updated_user_id' => 'Updated User ID',
            's_category_id' => 'Category',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 's_updated_user_id']);
    }

    public function getSCategory()
    {
        return $this->hasOne(SettingCategory::class, ['sc_id' => 's_category_id']);
    }

    /**
     * {@inheritdoc}
     * @return SettingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SettingQuery(static::class);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert && isset($changedAttributes['s_value'])) {
            $staff = Employee::getAllEmployeesByRole([Employee::ROLE_SUPER_ADMIN, Employee::ROLE_ADMIN]);
            /*foreach ($staff as $unit) {
                Notifications::create(
                    $unit->id,
                    'Setting Changed: (' . $this->s_id . ')',
                    'Site setting: ' . $this->s_name . ' (id:' . $this->s_id . ') has been changed from ' . $changedAttributes['s_value'] . ' to ' . $this->s_value . ' by ' . $this->sUpdatedUser->username,
                    Notifications::TYPE_INFO,
                    true
                );
            }*/
        }
    }
}
