<?php

namespace modules\taskList\src\entities\userTask;

use common\models\Employee;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\userTask\behaviors\UserTaskStatusLogDeleteBehavior;
use modules\taskList\src\events\UserTaskStatusChangedEvent;
use src\behaviors\dateTime\CreatedYearMonthBehavior;
use src\entities\EventTrait;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_task".
 *
 * @property int $ut_id
 * @property int $ut_user_id
 * @property string|null $ut_target_object
 * @property int $ut_target_object_id
 * @property int $ut_task_list_id
 * @property string|null $ut_description
 * @property string $ut_start_dt
 * @property string $ut_end_dt
 * @property int|null $ut_priority
 * @property int|null $ut_status_id
 * @property string|null $ut_created_dt
 * @property int $ut_year
 * @property int $ut_month
 *
 * @property ShiftScheduleEventTask[] $shiftScheduleEventTasks
 * @property UserShiftSchedule[] $userShiftEvents
 * @property Employee $user
 * @property TaskList $taskList
 * @property UserTaskStatusLog $completeTime
 */
class UserTask extends \yii\db\ActiveRecord
{
    use EventTrait;

    public const STATUS_PROCESSING = 1;
    public const STATUS_COMPLETE = 2;
    public const STATUS_CANCEL = 3;
    public const STATUS_FAILED = 4;

    public const STATUS_LIST = [
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_COMPLETE => 'Complete',
        self::STATUS_CANCEL => 'Cancel',
        self::STATUS_FAILED => 'Failed',
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

            [['ut_end_dt'], 'compare', 'compareAttribute' => 'ut_start_dt', 'operator' => '>=', 'enableClientValidation' => false, 'skipOnEmpty' => true],

            [['ut_created_dt'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['ut_created_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            [['ut_year'], 'integer'],

            [['ut_month'], 'integer'],

            [['ut_description'], 'string', 'max' => 255]
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'startDt' => [
                'class' => CreatedYearMonthBehavior::class,
                'createdColumn' => 'ut_start_dt',
                'yearColumn' => 'ut_year',
                'monthColumn' => 'ut_month',
            ],
            'deleteStatusLogs' => [
                'class' => UserTaskStatusLogDeleteBehavior::class
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
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

    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getUserShiftEvents()
    {
        return $this->hasMany(UserShiftSchedule::class, ['uss_id' => 'sset_event_id'])
            ->viaTable('shift_schedule_event_task', ['sset_user_task_id' => 'ut_id']);
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
            'ut_description' => 'Note',
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

    public function setStatusComplete(): UserTask
    {
        $this->ut_status_id = self::STATUS_COMPLETE;
        $this->recordStatusChangeEvent();

        return $this;
    }

    public function setStatusProcessing(): self
    {
        $this->ut_status_id = self::STATUS_PROCESSING;
        $this->recordStatusChangeEvent();

        return $this;
    }

    public function setStatusCancel(): self
    {
        $this->ut_status_id = self::STATUS_CANCEL;
        $this->recordStatusChangeEvent();

        return $this;
    }

    public function setStatusFailed(): self
    {
        $this->ut_status_id = self::STATUS_FAILED;
        $this->recordStatusChangeEvent();

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeadline(): bool
    {
        $deadline = false;
        if ($this->ut_end_dt) {
            if (time() > strtotime($this->ut_end_dt)) {
                $deadline = true;
            }
        }
        return $deadline;
    }

    /**
     * @return bool
     */
    public function isDelay(): bool
    {
        $delay = false;
        if ($this->ut_start_dt) {
            if (time() < strtotime($this->ut_start_dt)) {
                $delay = true;
            }
        }
        return $delay;
    }

    public function recordStatusChangeEvent(): void
    {
        $attributes = $this->getOldAttributes();

        $this->recordEvent(
            new UserTaskStatusChangedEvent($this, $this->ut_status_id, ($attributes['ut_status_id'] ?? null))
        );
    }

    public function isComplete(): bool
    {
        return $this->ut_status_id === self::STATUS_COMPLETE;
    }

    public function isProcessing(): bool
    {
        return $this->ut_status_id === self::STATUS_PROCESSING;
    }

    public function isCanceled(): bool
    {
        return $this->ut_status_id === self::STATUS_CANCEL;
    }

    public function isFailed(): bool
    {
        return $this->ut_status_id === self::STATUS_FAILED;
    }

    public function setOwner(int $newOwnerId): UserTask
    {
        $this->ut_user_id = $newOwnerId;
        return $this;
    }

    public function setStartDate(string $startDate): UserTask
    {
        $this->ut_start_dt = $startDate;
        return $this;
    }

    public function setEndDate(?string $endDate): UserTask
    {
        $this->ut_end_dt = $endDate;
        return $this;
    }

    public function getLastStatusLogByStatusId(int $statusId): ?UserTaskStatusLog
    {
        return UserTaskStatusLog::find()
            ->where([
                'utsl_ut_id' => $this->ut_id,
                'utsl_new_status' => $statusId,
            ])
            ->limit(1)
            ->one();
    }

    public function isOwner(?int $userId): bool
    {
        return $this->ut_user_id === $userId;
    }

    /**
     * @return ActiveQuery
     */
    public function getCompleteTime(): ActiveQuery
    {
        return $this->hasOne(UserTaskStatusLog::class, ['utsl_ut_id' => 'ut_id'])
            ->where([
                'utsl_new_status' => UserTask::STATUS_COMPLETE,
            ])
            ->orderBy([
                'utsl_created_dt' => SORT_DESC,
            ]);
    }
}
