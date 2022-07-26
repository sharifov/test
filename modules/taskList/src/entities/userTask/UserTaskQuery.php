<?php

namespace modules\taskList\src\entities\userTask;

use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;

class UserTaskQuery
{
    public static function getUserTaskCompletion(
        int $taskListId,
        int $userId,
        string $targetObject,
        int $targetObjectId,
        array $utStatusIds,
        array $userShiftScheduleStatuses,
        \DateTimeImmutable $dtNow,
        ?array $excludeIds = null
    ): UserTaskScopes {
        $userTasksQuery = UserTask::find()
            ->innerJoin([
                'shift_schedule_event_task_query' => ShiftScheduleEventTask::find()
                    ->select(['sset_user_task_id'])
                    ->innerJoin(
                        UserShiftSchedule::tableName(),
                        'sset_event_id = uss_id 
                            AND uss_start_utc_dt <= :dtNow AND uss_end_utc_dt >= :dtNow
                            AND uss_status_id IN (:statuses)',
                        [
                            'dtNow' => $dtNow->format('Y-m-d H:i:s'),
                            'statuses' => $userShiftScheduleStatuses
                        ]
                    )
                    ->groupBy(['sset_user_task_id'])
            ], 'ut_id = shift_schedule_event_task_query.sset_user_task_id')
            ->where(['ut_task_list_id' => $taskListId])
            ->andWhere(['ut_user_id' => $userId])
            ->andWhere(['ut_target_object' => $targetObject])
            ->andWhere(['ut_target_object_id' => $targetObjectId])
            ->andWhere(['IN', 'ut_status_id', $utStatusIds]);

        if (!empty($excludeIds)) {
            $userTasksQuery->andWhere(['NOT IN', 'ut_id', $excludeIds]);
        }

        $userTasksQuery->orderBy(['ut_created_dt' => SORT_ASC]);

        return $userTasksQuery;
    }
}
