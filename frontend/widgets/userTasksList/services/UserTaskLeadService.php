<?php

namespace frontend\widgets\userTasksList\services;

use frontend\widgets\userTasksList\helpers\UserTasksListHelper;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\taskList\src\entities\userTask\UserTaskQuery;
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

        if (empty(UserTasksListHelper::getCacheDuration())) {
            TagDependency::invalidate(\Yii::$app->cache, [
                'tags' => UserTasksListHelper::getCacheTagKey($lead->id, $lead->employee_id),
            ]);
        }

        $scheduleCacheKey = UserTasksListHelper::getShiftScheduleCacheKey($lead->id, $lead->employee_id, $pageNumb, $activeUserShiftScheduleId);
        $userShiftSchedules = $this->getUserShiftSchedules($lead, $scheduleCacheKey);

        if (!empty($userShiftSchedules)) {
            $scheduleTasksCacheKey = UserTasksListHelper::getSchedulesTasksCacheKey($lead->id, $lead->employee_id, $pageNumb, $activeUserShiftScheduleId);
            $schedulesData = $this->getSchedulesData($lead, $userShiftSchedules, $activeUserShiftScheduleId);
            $scheduleTasksData = $this->getScheduleTasksData($lead, $schedulesData['activeSchedule'], $scheduleTasksCacheKey);

            $result = [
                'userShiftSchedules' => $schedulesData['schedules'],
                'shiftScheduleTasks' => $scheduleTasksData['shiftScheduleTasks'],
                'activeShiftScheduleId' => $schedulesData['activeUserShiftScheduleId'],
                'shiftScheduleTasksPagination' => $scheduleTasksData['shiftScheduleTasksPagination'],
            ];

            /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
            if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
                $message = [
                    'leadId' => $lead->id,
                    'employee_id' => $lead->employee_id,
                    'shiftScheduleTasks' => $result['shiftScheduleTasks'],
                ];

                \Yii::info($message, 'info\UserTaskLeadServiceWidget:getSchedulesWithTasksPagination');
            }
        }

        return $result;
    }

    /**
     * @param Lead $lead
     * @param string $schedulesCacheKey
     * @return array
     */
    private function getUserShiftSchedules(Lead $lead, string $schedulesCacheKey): array
    {
        $cache = \Yii::$app->cache;
        $cacheTagKey = UserTasksListHelper::getCacheTagKey($lead->id, $lead->employee_id);
        $cacheTagDependency = UserTasksListHelper::getCacheTagDependency($cacheTagKey);

        $result = $cache->getOrSet($schedulesCacheKey, function () use ($lead) {
            $query = UserShiftScheduleQuery::getAllThatHaveTasksByLeadIdAndUserIdAndType($lead->id, $lead->employee_id)
                ->with('shiftScheduleEventTask');

            /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
            if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
                \Yii::info($query->createCommand()->getRawSql(), 'info\UserTaskLeadServiceWidget:getUserShiftSchedules:query');
            }

            return $query->all();
        }, UserTasksListHelper::getCacheDuration(), $cacheTagDependency);

        /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
            $message = [
                'leadId' => $lead->id,
                'employee_id' => $lead->employee_id,
                'userShiftSchedules' => $result,
            ];

            \Yii::info($message, 'info\UserTaskLeadServiceWidget:getUserShiftSchedules');
        }

        return $result;
    }

    /**
     * @param Lead $lead
     * @param UserShiftSchedule $activeScheduleData
     * @param string $cacheScheduleTasksKey
     * @return array
     */
    private function getScheduleTasksData(Lead $lead, UserShiftSchedule $activeScheduleData, string $cacheScheduleTasksKey): array
    {
        $cache = \Yii::$app->cache;
        $cacheTagKey = UserTasksListHelper::getCacheTagKey($lead->id, $lead->employee_id);
        $cacheTagDependency = UserTasksListHelper::getCacheTagDependency($cacheTagKey);

        $result = $cache->getOrSet($cacheScheduleTasksKey, function () use ($lead, $activeScheduleData) {
            $shiftScheduleTasksQuery = UserTaskQuery::getQueryUserIdAndTargetIdAndDateAndTargetObject(
                $lead->employee_id,
                $lead->id,
                $activeScheduleData->uss_start_utc_dt,
                $activeScheduleData->uss_end_utc_dt
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

            return [
                'shiftScheduleTasks' => $shiftScheduleTasks,
                'shiftScheduleTasksPagination' => $shiftScheduleTasksPagination,
            ];
        }, UserTasksListHelper::getCacheDuration(), $cacheTagDependency);

        if (empty($result['shiftScheduleTasks'])) {
            $cache->delete($cacheScheduleTasksKey);
        }

        return $result;
    }

    /**
     * @param Lead $lead
     * @param array $userShiftSchedules
     * @param int|null $activeUserShiftScheduleId
     * @return array
     */
    private function getSchedulesData(Lead $lead, array $userShiftSchedules, ?int $activeUserShiftScheduleId): array
    {
        $userShiftSchedulesNew = [];
        foreach ($userShiftSchedules as $shiftSchedules) {
            $eventId = $shiftSchedules->shiftScheduleEventTask['0']->sset_event_id;
            $userShiftSchedulesNew[$eventId] = $shiftSchedules;
        }

        $activeUserShiftScheduleId = $activeUserShiftScheduleId ?: reset($userShiftSchedulesNew)['uss_id'];
        $activeScheduleData = $activeUserShiftScheduleId ? $userShiftSchedulesNew[$activeUserShiftScheduleId] : reset($userShiftSchedulesNew);

        /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
            $message = [
                'leadId' => $lead->id,
                'employee_id' => $lead->employee_id,
                'userShiftSchedulesNew' => $userShiftSchedulesNew,
                'activeUserShiftScheduleId' => $activeUserShiftScheduleId,
                'activeScheduleData' => $activeScheduleData,
            ];

            \Yii::info($message, 'info\UserTaskLeadServiceWidget:getSchedulesData');
        }

        return [
            'activeSchedule' => $activeScheduleData,
            'schedules' => $userShiftSchedulesNew,
            'activeUserShiftScheduleId' => $activeUserShiftScheduleId,
        ];
    }
}
