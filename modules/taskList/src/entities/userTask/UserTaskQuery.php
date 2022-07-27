<?php

namespace modules\taskList\src\entities\userTask;

use Yii;

class UserTaskQuery
{
    public static function getUserTaskCompletion(
        int $taskListId,
        int $userId,
        string $targetObject,
        int $targetObjectId,
        array $utStatusIds,
        ?array $excludeIds = null
    ): UserTaskScopes {
        $userTasksQuery = UserTask::find()
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


    /**
     * @param int $userId
     * @param string $startDt
     * @param string $endDt
     * @return UserTaskScopes
     */
    private static function getQueryTaskListByUser(int $userId, string $startDt, string $endDt): UserTaskScopes
    {
        $startDateTime = date('Y-m-d H:i', strtotime($startDt));
        $endDateTime = date('Y-m-d H:i', strtotime($endDt));

        return UserTask::find()
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
    }

    /**
     * @param int $userId
     * @param string $startDt
     * @param string $endDt
     * @return array
     */
    public static function getTaskListByUser(int $userId, string $startDt, string $endDt): array
    {
        return self::getQueryTaskListByUser($userId, $startDt, $endDt)->all();
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
                    ]
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
}
