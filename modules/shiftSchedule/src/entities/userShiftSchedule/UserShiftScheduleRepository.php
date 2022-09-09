<?php

namespace modules\shiftSchedule\src\entities\userShiftSchedule;

use modules\taskList\src\entities\TargetObject;
use src\helpers\app\AppHelper;
use src\repositories\AbstractRepositoryWithEvent;

class UserShiftScheduleRepository extends AbstractRepositoryWithEvent
{
    public function __construct(UserShiftSchedule $model)
    {
        parent::__construct($model);
    }

    public function getModel(): UserShiftSchedule
    {
        return $this->model;
    }

    /**
     * Will get all user shift schedules(considering the parameters you passed) that have tasks inside
     *
     * @param int $leadId
     * @param int $userId
     * @param string $type
     * @return array
     */
    public function getAllThatHaveTasksByLeadIdAndUserIdAndType(int $leadId, int $userId, string $type = TargetObject::TARGET_OBJ_LEAD): array
    {
        $db = \Yii::$app->db;

        try {
            $result =  $this->model->find()
                ->select([
                    'user_shift_schedule.*',
                    'shift_schedule_event_task.sset_event_id',
                ])->innerJoin('shift_schedule_event_task', '
                    `user_shift_schedule`.`uss_user_id` = ' . $db->quoteValue($userId) . ' AND 
                    `user_shift_schedule`.`uss_id` = `shift_schedule_event_task`.`sset_event_id`
                ')->innerJoin('user_task', '
                    `user_task`.`ut_target_object_id` = ' . $db->quoteValue($leadId) . ' AND 
                    `user_task`.`ut_target_object` = ' . $db->quoteValue($type) . ' AND
                    `user_task`.`ut_id`= `shift_schedule_event_task`.`sset_user_task_id` AND
                    `user_task`.`ut_user_id` = `user_shift_schedule`.`uss_user_id`
                ')->orderBy([
                    'user_shift_schedule.uss_start_utc_dt' => SORT_ASC,
                ])
                ->indexBy('sset_event_id')
                ->asArray()
                ->all() ?: [];
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e), 'UserShiftScheduleRepository:getAllBy:Throwable');
        }

        return $result;
    }
}
