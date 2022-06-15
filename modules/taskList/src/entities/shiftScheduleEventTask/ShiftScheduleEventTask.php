<?php

namespace modules\taskList\src\entities\shiftScheduleEventTask;

use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\taskList\src\entities\userTask\UserTask;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "shift_schedule_event_task".
 *
 * @property int $sset_event_id
 * @property int $sset_user_task_id
 * @property string|null $sset_created_dt
 *
 * @property UserTask $ssetUserTask
 * @property UserShiftSchedule $userShiftSchedule
 */
class ShiftScheduleEventTask extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['sset_event_id', 'sset_user_task_id'], 'unique', 'targetAttribute' => ['sset_event_id', 'sset_user_task_id']],

            [['sset_created_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            [['sset_event_id'], 'required'],
            [['sset_event_id'], 'integer'],
            [['sset_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserShiftSchedule::class, 'targetAttribute' => ['sset_event_id' => 'uss_id']],

            [['sset_user_task_id'], 'required'],
            [['sset_user_task_id'], 'integer'],
            [['sset_user_task_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserTask::class, 'targetAttribute' => ['sset_user_task_id' => 'ut_id']],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['sset_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => false,
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getSsetUserTask(): \yii\db\ActiveQuery
    {
        return $this->hasOne(UserTask::class, ['ut_id' => 'sset_user_task_id']);
    }

    public function getUserShiftSchedule(): \yii\db\ActiveQuery
    {
        return $this->hasOne(UserShiftSchedule::class, ['uss_id' => 'sset_event_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'sset_event_id' => 'UserShiftSchedule Event ID',
            'sset_user_task_id' => 'User Task ID',
            'sset_created_dt' => 'Created Dt',
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
