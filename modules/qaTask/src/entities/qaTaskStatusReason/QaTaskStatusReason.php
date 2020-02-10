<?php

namespace modules\qaTask\src\entities\qaTaskStatusReason;

use common\models\Employee;
use modules\qaTask\src\entities\QaObjectType;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%qa_task_status_reason}}".
 *
 * @property int $tsr_id
 * @property int $tsr_object_type_id
 * @property int $tsr_status_id
 * @property string $tsr_key
 * @property string $tsr_name
 * @property string|null $tsr_description
 * @property int $tsr_comment_required
 * @property int $tsr_enabled
 * @property int|null $tsr_created_user_id
 * @property int|null $tsr_updated_user_id
 * @property string|null $tsr_created_dt
 * @property string|null $tsr_updated_dt
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class QaTaskStatusReason extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%qa_task_status_reason}}';
    }

    public function rules(): array
    {
        return [
            ['tsr_object_type_id', 'required'],
            ['tsr_object_type_id', 'integer'],
            ['tsr_object_type_id', 'in', 'range' => array_keys(QaObjectType::getList())],

            ['tsr_status_id', 'required'],
            ['tsr_status_id', 'integer'],
            ['tsr_status_id', 'in', 'range' => array_keys(QaTaskStatus::getList())],

            ['tsr_key', 'required'],
            ['tsr_key', 'string', 'max' => 30],
            ['tsr_key', 'unique'],

            ['tsr_name', 'required'],
            ['tsr_name', 'string', 'max' => 30],

            ['tsr_description', 'string', 'max' => 255],

            ['tsr_comment_required', 'required'],
            ['tsr_comment_required', 'boolean'],

            ['tsr_enabled', 'required'],
            ['tsr_enabled', 'boolean'],

            [['tsr_object_type_id', 'tsr_status_id', 'tsr_name'], 'unique', 'targetAttribute' => ['tsr_object_type_id', 'tsr_status_id', 'tsr_name']],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['tsr_created_dt', 'tsr_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['tsr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'tsr_created_user_id',
                'updatedByAttribute' => 'tsr_updated_user_id',
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'tsr_id' => 'ID',
            'tsr_object_type_id' => 'Object Type',
            'tsr_status_id' => 'Status',
            'tsr_key' => 'Key',
            'tsr_name' => 'Name',
            'tsr_description' => 'Description',
            'tsr_comment_required' => 'Comment Required',
            'tsr_enabled' => 'Enabled',
            'tsr_created_user_id' => 'Created User',
            'createdUser' => 'Created User',
            'tsr_updated_user_id' => 'Updated User',
            'updatedUser' => 'Updated User',
            'tsr_created_dt' => 'Created Dt',
            'tsr_updated_dt' => 'Updated Dt',
        ];
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'tsr_created_user_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'tsr_updated_user_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
