<?php

namespace modules\taskList\src\entities\userTask;

use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use modules\taskList\src\entities\TargetObject;
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
     * @param bool $isOnlyProcessing
     * @return UserTaskScopes
     */
    private static function getQueryTaskListByUser(int $userId, string $startDt, string $endDt, bool $isOnlyProcessing = false): UserTaskScopes
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

        if ($isOnlyProcessing) {
            return $userTasks->andWhere(['ut_status_id' => UserTask::STATUS_PROCESSING]);
        }

        return $userTasks;
    }

    /**
     * @param int $userId
     * @param string $startDt
     * @param string $endDt
     * @param bool $isOnlyProcessing
     * @return array
     */
    public static function getTaskListByUser(int $userId, string $startDt, string $endDt, bool $isOnlyProcessing = false): array
    {
        return self::getQueryTaskListByUser($userId, $startDt, $endDt, $isOnlyProcessing)->all();
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
}
