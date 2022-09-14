<?php

namespace frontend\widgets\userTasksList\services;

use frontend\widgets\userTasksList\helpers\UserTasksListHelper;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\taskList\src\entities\userTask\UserTaskQuery;
use src\auth\Auth;
use yii\caching\TagDependency;
use yii\data\Pagination;
use common\models\Lead;
use modules\featureFlag\FFlag;

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

        if (empty($lead->employee_id)) {
            return $result;
        }

        $cache = \Yii::$app->cache;
        $userShiftSchedules = $this->getUserShiftSchedules($lead);
        $cacheDuration = UserTasksListHelper::getCacheDuration();

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
            if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
                $message = [
                    'leadId' => $lead->id,
                    'employee_id' => $lead->employee_id,
                    'userShiftSchedulesNew' => $userShiftSchedulesNew,
                    'activeUserShiftScheduleId' => $activeUserShiftScheduleId,
                    'activeScheduleData' => $activeScheduleData,
                    'cacheKey' => $cacheKey,
                ];

                \Yii::info($message, 'info\UserTaskLeadServiceWidget:getSchedulesWithTasksPagination:second');
            }

            if (empty($cache->get($cacheKey)) || ($cacheDuration <= 0)) {
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

                $result = [
                    'userShiftSchedules' => $userShiftSchedulesNew,
                    'shiftScheduleTasks' => $shiftScheduleTasks,
                    'activeShiftScheduleId' => $activeUserShiftScheduleId,
                    'shiftScheduleTasksPagination' => $shiftScheduleTasksPagination,
                ];

                if ($cacheDuration > 0) {
                    $cache->set($cacheKey, $result, $cacheDuration, $cacheTag);
                }

                /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
                if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
                    $message = [
                        'leadId' => $lead->id,
                        'employee_id' => $lead->employee_id,
                        'cacheTag' => $cacheTag,
                        'shiftScheduleTasksQuery' => $shiftScheduleTasksQuery,
                        'shiftScheduleTasks' => $shiftScheduleTasks,
                        'cacheDuration' => $cacheDuration,
                    ];

                    \Yii::info($message, 'info\UserTaskLeadServiceWidget:getSchedulesWithTasksPagination:third');
                }
            } else {
                $result = $cache->get($cacheKey) ?: [];
            }
        }

        return $result;
    }

    private function getUserShiftSchedules($lead): array
    {
        $cache = \Yii::$app->cache;
        $cacheKeyOfSchedules = UserTasksListHelper::generateUserSchedulesListCacheKey($lead->id, $lead->employee_id);
        $cacheDuration = UserTasksListHelper::getCacheDuration();

        if (empty($cache->get($cacheKeyOfSchedules)) || $cacheDuration <= 0) {
            $query = UserShiftScheduleQuery::getAllThatHaveTasksByLeadIdAndUserIdAndType($lead->id, $lead->employee_id)
                ->with('shiftScheduleEventTask');

            /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
            if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
                \Yii::info($query->createCommand()->getRawSql(), 'info\UserTaskLeadServiceWidget:getAllThatHaveTasksByLeadIdAndUserIdAndType:query');
            }

            $result = $query->all();

            if ($cacheDuration > 0) {
                $cache->set($cacheKeyOfSchedules, $result, $cacheDuration);
            }
        } else {
            $result = $cache->get($cacheKeyOfSchedules) ?: [];
        }

        /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
            $message = [
                'leadId' => $lead->id,
                'employee_id' => $lead->employee_id,
                'cacheKeyOfSchedules' => $cacheKeyOfSchedules,
                'userShiftSchedules' => $result,
            ];

            \Yii::info($message, 'info\UserTaskLeadServiceWidget:getSchedulesWithTasksPagination:first');
        }

        return $result;
    }
}
