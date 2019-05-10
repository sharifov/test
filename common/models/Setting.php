<?php

namespace common\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "setting".
 *
 * @property int $s_id
 * @property string $s_key
 * @property string $s_name
 * @property string $s_type
 * @property string $s_value
 * @property string $s_updated_dt
 * @property int $s_updated_user_id
 *
 * @property Employee $sUpdatedUser
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
            [['s_updated_user_id'], 'integer'],
            [['s_key', 's_name', 's_value'], 'string', 'max' => 255],
            [['s_type'], 'string', 'max' => 10],
            [['s_key'], 'unique'],
            [['s_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['s_updated_user_id' => 'id']],
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
            's_type' => 'Type',
            's_value' => 'Value',
            's_updated_dt' => 'Updated Dt',
            's_updated_user_id' => 'Updated User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 's_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return SettingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SettingQuery(get_called_class());
    }
}
