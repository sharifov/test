<?php

namespace modules\taskList\src\entities\shiftScheduleEventTask;

use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskStatusLog;

class ShiftScheduleEventTaskQuery
{
    public static function getAllByLeadId(int $lead_id, ?int $user_id): ShiftScheduleEventTaskScopes
    {
        $query = ShiftScheduleEventTask::find()
            ->select([
                'shift_schedule_event_task.*',
                'task_list.tl_title',
                'user_task.*',
                'complete_time' => UserTaskStatusLog::find()
                    ->select(['utsl_created_dt'])
                    ->where([
                        'utsl_new_status' => UserTask::STATUS_COMPLETE,
                    ])
                    ->andWhere('user_task_status_log.utsl_ut_id = user_task.ut_id')
                    ->orderBy('utsl_created_dt DESC')
                    ->limit(1)])
            ->joinWith('userShiftSchedule', true, 'INNER JOIN')
            ->innerJoin('user_task', 'user_task.ut_id = shift_schedule_event_task.sset_user_task_id')
            ->leftJoin('task_list', 'task_list.tl_id = user_task.ut_task_list_id')
            ->innerJoin([
                'user_task_query' => UserTask::find()
                    ->select(['ut_id'])
                    ->andWhere(['ut_target_object' => TargetObject::TARGET_OBJ_LEAD])
                    ->andWhere(['ut_target_object_id' => $lead_id])
            ], 'user_task_query.ut_id = shift_schedule_event_task.sset_user_task_id')
            ->orderBy(['user_shift_schedule.uss_start_utc_dt' => SORT_ASC, 'task_list.tl_title' => SORT_ASC]);

        if (!empty($user_id)) {
            $query->andWhere(['user_shift_schedule.uss_user_id' => $user_id]);
        }

        return $query;
    }
}
