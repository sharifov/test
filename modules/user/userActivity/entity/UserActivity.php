<?php

namespace modules\user\userActivity\entity;

use common\models\Employee;
use kak\clickhouse\ActiveRecord;
use kak\clickhouse\Connection;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_activity".
 *
 * @property int $ua_user_id
 * @property string $ua_object_event
 * @property int $ua_object_id
 * @property string $ua_start_dt
 * @property string $ua_end_dt
 * @property int $ua_type_id
 * @property int|null $ua_shift_event_id
 * @property-read mixed $id
 * @property string $ua_description
 *
 * @property Employee $user
 */
class UserActivity extends ActiveRecord
{
    public const TYPE_MONITORING = 1;

    public const TYPE_LIST = [
        self::TYPE_MONITORING => 'Monitoring'
    ];

    /**
    * Get table name
    * @return string
    */
    public static function tableName(): string
    {
        return 'user_activity';
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey(): array
    {
        return ['ua_start_dt', 'ua_user_id', 'ua_object_event', 'ua_object_id'];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return Connection the ClickHouse connection used by this AR class.
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection
    {
        return Yii::$app->get('clickhouse');
    }


    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['ua_user_id', 'ua_object_event', 'ua_start_dt'], 'required'],
            [['ua_user_id', 'ua_object_id', 'ua_type_id', 'ua_shift_event_id'], 'integer'],
            [['ua_object_event', 'ua_description'], 'string'],
            [['ua_start_dt', 'ua_end_dt'], 'safe']
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'ua_user_id' => 'User ID',
            'ua_object_event' => 'Object Event',
            'ua_object_id' => 'Object ID',
            'ua_start_dt' => 'Start DateTime',
            'ua_end_dt' => 'End DateTime',
            'ua_type_id' => 'Type ID',
            'ua_shift_event_id' => 'Shift Event ID',
            'ua_description' => 'Description',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ua_user_id']);
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->ua_object_event;
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
    public function getTypeName(): string
    {
        return self::TYPE_LIST[$this->ua_type_id] ?? '-';
    }
}
