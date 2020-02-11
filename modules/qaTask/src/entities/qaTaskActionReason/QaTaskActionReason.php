<?php

namespace modules\qaTask\src\entities\qaTaskActionReason;

use common\models\Employee;
use modules\qaTask\src\entities\QaObjectType;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%qa_task_action_reason}}".
 *
 * @property int $tar_id
 * @property int $tar_object_type_id
 * @property int $tar_action_id
 * @property string $tar_key
 * @property string $tar_name
 * @property string|null $tar_description
 * @property int $tar_comment_required
 * @property int $tar_enabled
 * @property int|null $tar_created_user_id
 * @property int|null $tar_updated_user_id
 * @property string|null $tar_created_dt
 * @property string|null $tar_updated_dt
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class QaTaskActionReason extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%qa_task_action_reason}}';
    }

    public function rules(): array
    {
        return [
            ['tar_object_type_id', 'required'],
            ['tar_object_type_id', 'integer'],
            ['tar_object_type_id', 'in', 'range' => array_keys(QaObjectType::getList())],

            ['tar_action_id', 'required'],
            ['tar_action_id', 'integer'],
            ['tar_action_id', 'in', 'range' => array_keys(QaTaskActions::getList())],

            ['tar_key', 'required'],
            ['tar_key', 'string', 'max' => 30],
            ['tar_key', 'unique'],

            ['tar_name', 'required'],
            ['tar_name', 'string', 'max' => 30],

            ['tar_description', 'string', 'max' => 255],

            ['tar_comment_required', 'required'],
            ['tar_comment_required', 'boolean'],

            ['tar_enabled', 'required'],
            ['tar_enabled', 'boolean'],

            [['tar_object_type_id', 'tar_action_id', 'tar_name'], 'unique', 'targetAttribute' => ['tar_object_type_id', 'tar_action_id', 'tar_name']],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['tar_created_dt', 'tar_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['tar_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'tar_created_user_id',
                'updatedByAttribute' => 'tar_updated_user_id',
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'tar_id' => 'ID',
            'tar_object_type_id' => 'Object Type',
            'tar_action_id' => 'Action',
            'tar_key' => 'Key',
            'tar_name' => 'Name',
            'tar_description' => 'Description',
            'tar_comment_required' => 'Comment Required',
            'tar_enabled' => 'Enabled',
            'tar_created_user_id' => 'Created User',
            'createdUser' => 'Created User',
            'tar_updated_user_id' => 'Updated User',
            'updatedUser' => 'Updated User',
            'tar_created_dt' => 'Created Dt',
            'tar_updated_dt' => 'Updated Dt',
        ];
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'tar_created_user_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'tar_updated_user_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
