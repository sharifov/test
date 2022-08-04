<?php

namespace modules\taskList\src\entities\shiftScheduleEventTask;

use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\userTask\UserTask;

class ShiftScheduleEventTaskQuery
{
    public static function getAllByLeadId(int $lead_id): ShiftScheduleEventTaskScopes
    {
        return ShiftScheduleEventTask::find()
            ->joinWith('userShiftSchedule')
            ->innerJoin('user_task', 'user_task.ut_id = shift_schedule_event_task.sset_user_task_id')
            ->leftJoin('task_list', 'task_list.tl_id = user_task.ut_task_list_id ')
            ->innerJoin([
                'user_task_query' => UserTask::find()
                    ->select(['ut_id'])
                    ->andWhere(['ut_target_object' => TargetObject::TARGET_OBJ_LEAD])
                    ->andWhere(['ut_target_object_id' => $lead_id])
            ], 'user_task_query.ut_id = shift_schedule_event_task.sset_user_task_id')
            ->orderBy(['user_shift_schedule.uss_start_utc_dt' => SORT_ASC, 'task_list.tl_title' => SORT_ASC]);
    }
}
