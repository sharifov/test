<?php

namespace modules\qaTask\src\entities\qaTaskStatusLog;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatusAction;
use modules\qaTask\src\entities\qaTaskStatusReason\QaTaskStatusReason;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%qa_task_status_log}}".
 *
 * @property int $tsl_id
 * @property int $tsl_task_id
 * @property int|null $tsl_start_status_id
 * @property int $tsl_end_status_id
 * @property string $tsl_start_dt
 * @property string|null $tsl_end_dt
 * @property int|null $tsl_duration
 * @property int|null $tsl_reason_id
 * @property string|null $tsl_description
 * @property int|null $tsl_assigned_user_id
 * @property int|null $tsl_created_user_id
 * @property int|null $tsl_action_id
 *
 * @property Employee $createdUser
 * @property Employee $assignedUser
 * @property QaTask $task
 * @property QaTaskStatusReason $reason
 */
class QaTaskStatusLog extends \yii\db\ActiveRecord
{
    public static function create(CreateDto $dto): self
    {
        $log = new static();
        $log->tsl_task_id = $dto->taskId;
        $log->tsl_start_status_id = $dto->startStatusId;
        $log->tsl_end_status_id = $dto->endStatusId;
        $log->tsl_reason_id = $dto->reasonId;
        $log->tsl_description = $dto->description;
        $log->tsl_action_id = $dto->actionId;
        $log->tsl_assigned_user_id = $dto->assignedId;
        $log->tsl_created_user_id = $dto->creatorId;
        $log->tsl_start_dt = date('Y-m-d H:i:s');
        return $log;
    }

    public function end(): void
    {
        $this->tsl_end_dt = date('Y-m-d H:i:s');
        $this->tsl_duration = (int) (strtotime($this->tsl_end_dt) - strtotime($this->tsl_start_dt));
    }

    public static function tableName(): string
    {
        return '{{%qa_task_status_log}}';
    }

    public function rules(): array
    {
        return [
            ['tsl_task_id', 'required'],
            ['tsl_task_id', 'integer'],
            ['tsl_task_id', 'exist', 'skipOnError' => true, 'targetClass' => QaTask::class, 'targetAttribute' => ['tsl_task_id' => 't_id']],

            ['tsl_start_status_id', 'integer'],
            ['tsl_start_status_id', 'in', 'range' => array_keys(QaTaskStatus::getList())],

            ['tsl_end_status_id', 'required'],
            ['tsl_end_status_id', 'integer'],
            ['tsl_end_status_id', 'in', 'range' => array_keys(QaTaskStatus::getList())],

            ['tsl_start_dt', 'required'],
            ['tsl_start_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['tsl_end_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['tsl_duration', 'integer'],

            ['tsl_reason_id', 'integer'],
            ['tsl_reason_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['tsl_reason_id', 'exist', 'skipOnError' => true, 'targetClass' => QaTaskStatusReason::class, 'targetAttribute' => ['tsl_reason_id' => 'tsr_id']],
            ['tsl_reason_id', function () {
                if ($this->reason->tsr_object_type_id !== $this->task->t_object_type_id) {
                    $this->addError('tsl_reason_id', 'Different types Reason and Task');
                }
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            ['tsl_description', 'string', 'max' => 255],

            ['tsl_action_id', 'integer'],
            ['tsl_action_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['tsl_action_id', 'in', 'range' => array_keys(QaTaskStatusAction::getList())],

            ['tsl_assigned_user_id', 'integer'],
            ['tsl_assigned_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['tsl_assigned_user_id' => 'id']],

            ['tsl_created_user_id', 'integer'],
            ['tsl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['tsl_created_user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'tsl_id' => 'ID',
            'tsl_task_id' => 'Task ID',
            'task' => 'Task',
            'tsl_start_status_id' => 'Start Status',
            'tsl_end_status_id' => 'End Status',
            'tsl_start_dt' => 'Start Dt',
            'tsl_end_dt' => 'End Dt',
            'tsl_reason_id' => 'Reason',
            'reason.tsr_name' => 'Reason',
            'tsl_description' => 'Description',
            'tsl_duration' => 'Duration',
            'tsl_action_id' => 'Action',
            'tsl_assigned_user_id' => 'Assigned User',
            'assignedUser' => 'Assigned User',
            'tsl_created_user_id' => 'Created User',
            'createdUser' => 'Created User',
        ];
    }

    public function getReason(): ActiveQuery
    {
        return $this->hasOne(QaTaskStatusReason::class, ['tsr_id' => 'tsl_reason_id']);
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'tsl_created_user_id']);
    }

    public function getAssignedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'tsl_assigned_user_id']);
    }

    public function getTask(): ActiveQuery
    {
        return $this->hasOne(QaTask::class, ['t_id' => 'tsl_task_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
