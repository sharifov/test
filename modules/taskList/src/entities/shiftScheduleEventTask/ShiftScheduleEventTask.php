<?php

namespace modules\taskList\src\entities\shiftScheduleEventTask;

use modules\taskList\src\entities\userTask\UserTask;
use Yii;

/**
 * This is the model class for table "shift_schedule_event_task".
 *
 * @property int $sset_event_id
 * @property int $sset_user_task_id
 * @property string|null $sset_created_dt
 *
 * @property UserTask $ssetUserTask
 */
class ShiftScheduleEventTask extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['sset_event_id', 'sset_user_task_id'], 'unique', 'targetAttribute' => ['sset_event_id', 'sset_user_task_id']],

            ['sset_created_dt', 'safe'],

            ['sset_event_id', 'required'],
            ['sset_event_id', 'integer'],

            ['sset_user_task_id', 'required'],
            ['sset_user_task_id', 'integer'],
            ['sset_user_task_id', 'exist', 'skipOnError' => true, 'targetClass' => UserTask::class, 'targetAttribute' => ['sset_user_task_id' => 'ut_id']],
        ];
    }

    public function getSsetUserTask(): \yii\db\ActiveQuery
    {
        return $this->hasOne(UserTask::class, ['ut_id' => 'sset_user_task_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'sset_event_id' => 'Sset Event ID',
            'sset_user_task_id' => 'Sset User Task ID',
            'sset_created_dt' => 'Sset Created Dt',
        ];
    }

    public static function find(): ShiftScheduleEventTaskScopes
    {
        return new ShiftScheduleEventTaskScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'shift_schedule_event_task';
    }
}
