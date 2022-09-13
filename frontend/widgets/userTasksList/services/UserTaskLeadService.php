<?php

namespace frontend\widgets\userTasksList\services;

use frontend\widgets\userTasksList\helpers\UserTasksListHelper;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\taskList\src\entities\userTask\UserTaskQuery;
use src\auth\Auth;
use yii\caching\TagDependency;
use yii\data\Pagination;
use common\models\Lead;

class UserTaskLeadService
{
    /**
     * @param Lead $lead
     * @param int|null $activeUserShiftScheduleId
     * @return array
     */
    public function getSchedulesWithTasksPagination(Lead $lead, int $pageNumb = 1, ?int $activeUserShiftScheduleId = null): array
    {
        $result = [];
        $cache = \Yii::$app->cache;
        $cacheKeyOfSchedules = UserTasksListHelper::generateUserSchedulesListCacheKey($lead->id, $lead->employee_id);

        $userShiftSchedules = $cache->getOrSet($cacheKeyOfSchedules, static function () use ($lead) {
            return UserShiftScheduleQuery::getAllThatHaveTasksByLeadIdAndUserIdAndType($lead->id, $lead->employee_id)
                ->with('shiftScheduleEventTask')
                ->all();
        });

        /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
            $message = [
                'cacheKeyOfSchedules' => $cacheKeyOfSchedules,
                'userShiftSchedules' => $userShiftSchedules,
            ];

            \Yii::info($message, 'info\UserTaskLeadServiceWidget:getSchedulesWithTasksPagination:first');
        }

        if (!empty($userShiftSchedules)) {
            $userShiftSchedulesNew = [];
            foreach ($userShiftSchedules as $shiftSchedules) {
                $eventId = $shiftSchedules->shiftScheduleEventTask['0']->sset_event_id;
                $userShiftSchedulesNew[$eventId] = $shiftSchedules;
            }

            $activeUserShiftScheduleId = $activeUserShiftScheduleId ?: reset($userShiftSchedulesNew)['uss_id'];
            $activeScheduleData = $activeUserShiftScheduleId ? $userShiftSchedulesNew[$activeUserShiftScheduleId] : reset($userShiftSchedulesNew);

            $cacheKey = UserTasksListHelper::generateUserTasksListCacheKey(
                $lead->id,
                Auth::id(),
                $pageNumb,
                $activeUserShiftScheduleId
            );

            /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
            if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
                $message = [
                    'userShiftSchedulesNew' => $userShiftSchedulesNew,
                    'activeUserShiftScheduleId' => $activeUserShiftScheduleId,
                    'activeScheduleData' => $activeScheduleData,
                    'cacheKey' => $cacheKey,
                ];

                \Yii::info($message, 'info\UserTaskLeadServiceWidget:getSchedulesWithTasksPagination:second');
            }

            if (!$cache->get($cacheKey)) {
                $cacheTag = new TagDependency(['tags' => UserTasksListHelper::getUserTasksListCacheTag($lead->id, Auth::id())]);

                $shiftScheduleTasksQuery = UserTaskQuery::getQueryUserIdAndTargetIdAndDateAndTargetObject(
                    $lead->employee_id,
                    $lead->id,
                    $activeScheduleData['uss_start_utc_dt'],
                    $activeScheduleData['uss_end_utc_dt']
                );
                $shiftScheduleTasksPagination = new Pagination([
                    'route' => 'lead/pjax-user-tasks-list',
                    'pageSize' => UserTasksListHelper::PAGE_SIZE,
                    'totalCount' => (clone $shiftScheduleTasksQuery)->count(),
                ]);
                $shiftScheduleTasks = $shiftScheduleTasksQuery
                    ->offset($shiftScheduleTasksPagination->offset)
                    ->limit($shiftScheduleTasksPagination->limit)
                    ->asArray()
                    ->all();

                $cache->set($cacheKey, [
                    'userShiftSchedules' => $userShiftSchedulesNew,
                    'shiftScheduleTasks' => $shiftScheduleTasks,
                    'activeShiftScheduleId' => $activeUserShiftScheduleId,
                    'shiftScheduleTasksPagination' => $shiftScheduleTasksPagination,
                ], UserTasksListHelper::CACHE_DURATION, $cacheTag);

                /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
                if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
                    $message = [
                        'cacheTag' => $cacheTag,
                        'shiftScheduleTasksQuery' => $shiftScheduleTasksQuery,
                        'shiftScheduleTasks' => $shiftScheduleTasks,
                    ];

                    \Yii::info($message, 'info\UserTaskLeadServiceWidget:getSchedulesWithTasksPagination:third');
                }
            }

            $result = $cache->get($cacheKey);
        }

        return $result;
    }
}
