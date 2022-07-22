<?php

namespace modules\taskList\src\entities\userTask;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "user_task_status_log".
 *
 * @property int $utsl_id
 * @property int|null $utsl_ut_id
 * @property string|null $utsl_description
 * @property int $utsl_old_status
 * @property int $utsl_new_status
 * @property int|null $utsl_created_user_id
 * @property string|null $utsl_created_dt
 *
 * @property Employee $utslCreatedUser
 * @property UserTask $userTask
 */
class UserTaskStatusLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_task_status_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['utsl_ut_id', 'utsl_old_status', 'utsl_new_status', 'utsl_created_user_id'], 'integer'],

            [['utsl_ut_id'], 'required'],
            [['utsl_ut_id'], 'integer'],
            [['utsl_ut_id'], 'exist', 'skipOnError' => false, 'targetClass' => UserTask::class, 'targetAttribute' => ['utsl_ut_id' => 'ut_id']],

            [['utsl_old_status', 'utsl_new_status'], 'required'],
            [['utsl_old_status', 'utsl_new_status'], 'in', 'range' => array_keys(UserTask::STATUS_LIST)],
            ['utsl_old_status', 'compare', 'operator' => '!=', 'compareAttribute' => 'utsl_new_status'],

            [['utsl_created_dt'], 'safe'],

            [['utsl_description'], 'string', 'max' => 255],

            [['utsl_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['utsl_created_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'utsl_id' => 'ID',
            'utsl_ut_id' => 'User Task ID',
            'utsl_description' => 'Description',
            'utsl_old_status' => 'Old Status',
            'utsl_new_status' => 'New Status',
            'utsl_created_user_id' => 'Created User ID',
            'utsl_created_dt' => 'Created Datetime',
        ];
    }


    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['utsl_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['utsl_created_user_id'],
                ],
                'defaultValue' => null,
            ],
        ];
    }

    public function getUtslCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'utsl_created_user_id']);
    }

    public function getUserTask(): ActiveQuery
    {
        return $this->hasOne(UserTask::class, ['ut_id' => 'utsl_ut_id']);
    }

    public static function find(): UserTaskStatusLogScopes
    {
        return new UserTaskStatusLogScopes(static::class);
    }
}
