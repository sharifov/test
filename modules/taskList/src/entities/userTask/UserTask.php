<?php

namespace modules\taskList\src\entities\userTask;

use common\models\Employee;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\taskList\TaskList;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_task".
 *
 * @property int $ut_id
 * @property int $ut_user_id
 * @property string|null $ut_target_object
 * @property int $ut_target_object_id
 * @property int $ut_task_list_id
 * @property string $ut_start_dt
 * @property string $ut_end_dt
 * @property int|null $ut_priority
 * @property int|null $ut_status_id
 * @property string|null $ut_created_dt
 * @property int $ut_year
 * @property int $ut_month
 *
 * @property ShiftScheduleEventTask[] $shiftScheduleEventTasks
 * @property Employee $user
 * @property TaskList $taskList
 */
class UserTask extends \yii\db\ActiveRecord
{
    public const STATUS_PROCESSING = 1;
    public const STATUS_COMPLETE = 2;
    public const STATUS_CANCEL = 3;

    public const STATUS_LIST = [
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_COMPLETE => 'Complete',
        self::STATUS_CANCEL => 'Cancel',
    ];

    public const PRIORITY_LOW = 1;
    public const PRIORITY_MEDIUM = 2;
    public const PRIORITY_HIGH = 3;

    public const PRIORITY_LIST = [
        self::PRIORITY_LOW => 'Low',
        self::PRIORITY_MEDIUM => 'Medium',
        self::PRIORITY_HIGH => 'High',
    ];

    public function rules(): array
    {
        return [
            [['ut_priority'], 'integer'],
            [['ut_priority'], 'in', 'range' => array_keys(self::PRIORITY_LIST)],
            [['ut_priority'], 'default', 'value' => self::PRIORITY_MEDIUM],

            [['ut_status_id'], 'integer'],
            [['ut_status_id'], 'in', 'range' => array_keys(self::STATUS_LIST)],
            [['ut_status_id'], 'default', 'value' => self::STATUS_PROCESSING],

            [['ut_target_object'], 'required'],
            [['ut_target_object'], 'string', 'max' => 50],
            [['ut_target_object'], 'in', 'range' => array_keys(TargetObject::TARGET_OBJ_LIST)],

            [['ut_target_object_id'], 'required'],
            [['ut_target_object_id'], 'integer'],

            [['ut_task_list_id'], 'required'],
            [['ut_task_list_id'], 'integer'],
            [['ut_task_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskList::class, 'targetAttribute' => ['ut_task_list_id' => 'tl_id']],

            [['ut_user_id'], 'required'],
            [['ut_user_id'], 'integer'],
            [['ut_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ut_user_id' => 'id']],

            [['ut_start_dt'], 'required'],
            [['ut_start_dt', 'ut_end_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['ut_start_dt'], 'compare', 'compareAttribute' => 'ut_end_dt', 'operator' => '<=', 'enableClientValidation' => false],

            [['ut_created_dt'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['ut_created_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            [['ut_year'], 'integer'],
            [['ut_year'], 'default', 'value' => date('Y')],

            [['ut_month'], 'integer'],
            [['ut_month'], 'default', 'value' => date('m')],
        ];
    }

    public function getShiftScheduleEventTasks(): ActiveQuery
    {
        return $this->hasMany(ShiftScheduleEventTask::class, ['sset_user_task_id' => 'ut_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ut_user_id']);
    }

    public function getTaskList(): ActiveQuery
    {
        return $this->hasOne(TaskList::class, ['tl_id' => 'ut_task_list_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ut_id' => 'ID',
            'ut_user_id' => 'User ID',
            'ut_target_object' => 'Target Object',
            'ut_target_object_id' => 'Target Object ID',
            'ut_task_list_id' => 'Task List',
            'ut_start_dt' => 'Start Dt',
            'ut_end_dt' => 'End Dt',
            'ut_priority' => 'Priority',
            'ut_status_id' => 'Status',
            'ut_created_dt' => 'Created Dt',
            'ut_year' => 'Year',
            'ut_month' => 'Month',
        ];
    }

    public static function find(): UserTaskScopes
    {
        return new UserTaskScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'user_task';
    }

    public static function create(
        int $userId,
        string $targetObject,
        int $targetObjectId,
        int $taskListId,
        string $startDt,
        ?string $endDt = null,
        ?int $priorityId = null,
        ?int $statusId = null
    ): UserTask {
        $model = new self();
        $model->ut_user_id = $userId;
        $model->ut_target_object = $targetObject;
        $model->ut_target_object_id = $targetObjectId;
        $model->ut_task_list_id = $taskListId;
        $model->ut_start_dt = $startDt;
        $model->ut_end_dt = $endDt;
        $model->ut_priority = $priorityId;
        $model->ut_status_id = $statusId;

        return $model::fillSystemFields($model);
    }

    private static function fillSystemFields(UserTask $model): UserTask
    {
        $nowDT = (new \DateTimeImmutable());
        $model->ut_created_dt = $nowDT->format('Y-m-d H:i:s');
        $model->ut_year = $nowDT->format('Y');
        $model->ut_month = $nowDT->format('m');
        return $model;
    }

    public static function getStatusName(?int $statusId): string
    {
        return self::STATUS_LIST[$statusId] ?? '-';
    }

    public static function getPriorityName(?int $priorityId): string
    {
        return self::PRIORITY_LIST[$priorityId] ?? '-';
    }
}
