<?php

namespace modules\taskList\src\entities\userTask;

use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use modules\taskList\src\entities\TargetObject;
use src\helpers\app\AppHelper;
use src\helpers\app\DBHelper;
use Yii;

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
        ?\DateTimeImmutable $dTStart,
        ?\DateTime $dTEnd,
        ?array $excludeIds = null
    ): UserTaskScopes {
        $dtNowFormatted = $dtNow->format('Y-m-d H:i:s');

        $userTasksQuery = UserTask::find()
            ->select(UserTask::tableName() . '.*')
            ->innerJoin([
                'shift_schedule_event_task_query' => ShiftScheduleEventTask::find()
                    ->select(['sset_user_task_id'])
                    ->innerJoin([
                        'join_user_shift_schedule_query' => UserShiftSchedule::find()
                            ->select(['uss_id'])
                            ->innerJoin(
                                ShiftScheduleType::tableName(),
                                'uss_sst_id = sst_id',
                            )
                            ->where(['IN', 'uss_status_id', $userShiftScheduleStatuses])
                            ->andWhere(['<=', 'uss_start_utc_dt', $dtNowFormatted])
                            ->andWhere(['>=', 'uss_end_utc_dt', $dtNowFormatted])
                            ->andWhere(['sst_subtype_id' => ShiftScheduleType::SUBTYPE_WORK_TIME])
                            ->groupBy(['uss_id'])
                    ], 'sset_event_id = uss_id')
                    ->groupBy(['sset_user_task_id'])
            ], 'ut_id = shift_schedule_event_task_query.sset_user_task_id')
            ->where(['ut_task_list_id' => $taskListId])
            ->andWhere(['ut_user_id' => $userId])
            ->andWhere(['ut_target_object' => $targetObject])
            ->andWhere(['ut_target_object_id' => $targetObjectId])
            ->andWhere(['IN', 'ut_status_id', $utStatusIds])
            ->andWhere(['<=', 'ut_start_dt', $dtNowFormatted])
            ->andWhere(['OR', ['ut_end_dt' => null], ['>=', 'ut_end_dt', $dtNowFormatted]]);

        if ($dTStart && $dTEnd) {
            $userTasksQuery->andWhere(DBHelper::yearMonthRestrictionQuery(
                $dTStart,
                $dTEnd,
                'ut_year',
                'ut_month'
            ));
        }

        if (!empty($excludeIds)) {
            $userTasksQuery->andWhere(['NOT IN', 'ut_id', $excludeIds]);
        }

        $userTasksQuery->orderBy(['ut_created_dt' => SORT_ASC])->distinct();

        return $userTasksQuery;
    }


    /**
     * @param int $userId
     * @param string $startDt
     * @param string $endDt
     * @param array $statusList
     * @return UserTaskScopes
     */
    private static function getQueryTaskListByUser(int $userId, string $startDt, string $endDt, array $statusList): UserTaskScopes
    {
        $startDateTime = date('Y-m-d H:i', strtotime($startDt));
        $endDateTime = date('Y-m-d H:i', strtotime($endDt));

        $userTasks = UserTask::find()
            ->andWhere([
                'OR',
                ['between', 'ut_start_dt', $startDateTime, $endDateTime],
                ['between', 'ut_end_dt', $startDateTime, $endDateTime],
                [
                    'AND',
                    ['>=', 'ut_start_dt', $startDateTime],
                    ['<=', 'ut_end_dt', $endDateTime]
                ],
                [
                    'AND',
                    ['<=', 'ut_start_dt', $startDateTime],
                    ['>=', 'ut_end_dt', $endDateTime]
                ]
            ])
            ->andWhere(['ut_user_id' => $userId]);

        if (!empty($statusList)) {
            return $userTasks->andWhere(['IN', 'ut_status_id', $statusList]);
        }

        return $userTasks;
    }

    /**
     * @param int $userId
     * @param string $startDt
     * @param string $endDt
     * @param array $statusList
     * @return array
     */
    public static function getTaskListByUser(int $userId, string $startDt, string $endDt, array $statusList = []): array
    {
        return self::getQueryTaskListByUser($userId, $startDt, $endDt, $statusList)->all();
    }


    /**
     * @param UserTask[] $taskList
     * @param string $userTimeZone
     * @return array
     */
    public static function getCalendarTaskJsonData(array $taskList, string $userTimeZone): array
    {
        $data = [];
        if ($taskList) {
            foreach ($taskList as $item) {
                $dataItem = [
                    'id' => $item->ut_id,
                    'title' => $item->taskList->tl_title . ' (' . UserTask::getStatusName($item->ut_status_id) . ')',
                    'description' => $item->taskList->tl_title . "\r\n" . '(' . $item->ut_id . ')'
                        . ', status: ' . UserTask::getStatusName($item->ut_status_id)
                        . ', priority: ' . UserTask::getPriorityName($item->ut_priority),
                    'start' => Yii::$app->formatter->asDateTimeByUserTimezone(
                        strtotime($item->ut_start_dt ?? ''),
                        $userTimeZone,
                        'php: c'
                    ),
                    'end' => Yii::$app->formatter->asDateTimeByUserTimezone(
                        strtotime($item->ut_end_dt ?? ''),
                        $userTimeZone,
                        'php: c'
                    ),
                    'color' => 'gray', //$item->shiftScheduleType ? $item->shiftScheduleType->sst_color : 'gray',
                    'display' => 'list-item', //'block',
                    'extendedProps' => [
                        'type' => 'task',
                        'icon' => '', //fa fa-building-o',
                    ],
                    'typeEvent' => 'user-task'
                ];

//                if (
//                    !in_array($item->uss_status_id, [
//                        UserShiftSchedule::STATUS_DONE,
//                        UserShiftSchedule::STATUS_APPROVED
//                    ], true)
//                ) {
//                    $dataItem['borderColor'] = '#000000';
//                    $dataItem['title'] .= ' (' . $item->getStatusName() . ')';
//                    $dataItem['description'] .= ' (' . $item->getStatusName() . ')';
//                }

                $data[] = $dataItem;
            }
        }
        return $data;
    }

    public static function getQueryUserTaskByUserTaskList(int $userId, int $taskListId, string $targetObject, int $targetObjectId): UserTaskScopes
    {
        return UserTask::find()
            ->where(['ut_user_id' => $userId])
            ->andWhere(['ut_target_object' => $targetObject])
            ->andWhere(['ut_target_object_id' => $targetObjectId])
            ->andWhere(['ut_task_list_id' => $taskListId]);
    }

    public static function getQueryUserTaskByUserTaskListAndStatuses(int $userId, int $taskListId, string $targetObject, int $targetObjectId, array $statuses): UserTaskScopes
    {
        return self::getQueryUserTaskByUserTaskList($userId, $taskListId, $targetObject, $targetObjectId)->andWhere(['IN', 'ut_status_id', $statuses]);
    }

    public static function getQueryUserTaskByTargetIdAndStatuses(int $targetObjectId, array $statuses, array $targetObjects = [TargetObject::TARGET_OBJ_LEAD]): UserTaskScopes
    {
        return UserTask::find()
            ->andWhere(['ut_target_object' => $targetObjects])
            ->andWhere(['ut_target_object_id' => $targetObjectId])
            ->andWhere(['IN', 'ut_status_id', $statuses]);
    }

    public static function getQueryUserTaskByTargetObjectAndTaskList(int $taskListId, string $targetObject, int $targetObjectId): UserTaskScopes
    {
        return UserTask::find()
            ->andWhere(['ut_target_object' => $targetObject])
            ->andWhere(['ut_target_object_id' => $targetObjectId])
            ->andWhere(['ut_task_list_id' => $taskListId]);
    }

    public static function getQueryCompleteTime(): UserTaskStatusLogScopes
    {
        return UserTaskStatusLog::find()
            ->select(['utsl_created_dt'])
            ->where([
                'utsl_new_status' => UserTask::STATUS_COMPLETE,
            ])
            ->andWhere('user_task_status_log.utsl_ut_id = user_task.ut_id')
            ->orderBy('utsl_created_dt DESC')
            ->limit(1);
    }

    /**
     * @param int $userId
     * @param int $targetObjectId
     * @param string $startDate
     * @param string $endDate
     * @param string $targetObject
     * @return UserTaskScopes
     */
    public static function getQueryUserIdAndTargetIdAndDateAndTargetObject(
        int $userId,
        int $targetObjectId,
        string $startDate,
        string $endDate,
        string $targetObject = TargetObject::TARGET_OBJ_LEAD
    ): UserTaskScopes {
        $query = static::getQueryTaskListByUser($userId, $startDate, $endDate, []);
        $query->select([
            'user_task.*',
            'task_list.tl_title',
            'complete_time' => static::getQueryCompleteTime(),
        ]);

        $query->andWhere([
            'ut_target_object' => $targetObject,
            'ut_target_object_id' => $targetObjectId,
        ]);

        $query->innerJoin('task_list', 'user_task.ut_task_list_id = task_list.tl_id');

        return $query;
    }
}
