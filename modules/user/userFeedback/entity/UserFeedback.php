<?php

namespace modules\user\userFeedback\entity;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is the model class for table "user_feedback".
 *
 * @property int $uf_id
 * @property int $uf_type_id
 * @property int $uf_status_id
 * @property string $uf_title
 * @property string|null $uf_message
 * @property string $uf_data_json
 * @property string $uf_created_dt
 * @property string|null $uf_updated_dt
 * @property int|null $uf_created_user_id
 * @property int|null $uf_updated_user_id
 */
class UserFeedback extends ActiveRecord
{
    public const TYPE_BUG       = 1;
    public const TYPE_FEATURE   = 2;
    public const TYPE_QUESTION  = 3;

    public const STATUS_NEW         = 1;
    public const STATUS_PENDING     = 2;
    public const STATUS_CANCEL      = 3;
    public const STATUS_DONE        = 3;

    public const TYPE_LIST = [
        self::TYPE_BUG => 'Bug Report',
        self::TYPE_FEATURE => 'Feature',
        self::TYPE_QUESTION => 'Question'
    ];

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_feedback';
    }

    /**
     * @return Connection the database connection used by this AR class.
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection
    {
        return Yii::$app->get('db_postgres');
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['uf_id', 'uf_type_id', 'uf_status_id', 'uf_title', 'uf_data_json', 'uf_created_dt'], 'required'],
            [['uf_id', 'uf_type_id', 'uf_status_id', 'uf_created_user_id', 'uf_updated_user_id'], 'default', 'value' => null],
            [['uf_id', 'uf_type_id', 'uf_status_id', 'uf_created_user_id', 'uf_updated_user_id'], 'integer'],
            [['uf_message'], 'string'],
            [['uf_data_json', 'uf_created_dt', 'uf_updated_dt'], 'safe'],
            [['uf_title'], 'string', 'max' => 255],
            [['uf_id', 'uf_created_dt'], 'unique', 'targetAttribute' => ['uf_id', 'uf_created_dt']],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'uf_id' => 'ID',
            'uf_type_id' => 'Type ID',
            'uf_status_id' => 'Status ID',
            'uf_title' => 'Title',
            'uf_message' => 'Message',
            'uf_data_json' => 'Data Json',
            'uf_created_dt' => 'Created Dt',
            'uf_updated_dt' => 'Updated Dt',
            'uf_created_user_id' => 'Created User ID',
            'uf_updated_user_id' => 'Updated User ID',
        ];
    }

    /**
     * @return string[]
     */
    public static function getTypeList(): array
    {
        return self::TYPE_LIST;
    }
}
