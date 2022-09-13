<?php

namespace frontend\widgets\userTasksList\services;

use frontend\widgets\userTasksList\helpers\UserTasksListHelper;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\taskList\src\entities\userTask\UserTaskQuery;
use src\auth\Auth;
use yii\caching\TagDependency;
use yii\data\Pagination;

class UserTaskLeadService
{
    /**
     * @param int $leadId
     * @param int|null $activeUserShiftScheduleId
     * @return array
     */
    public function getSchedulesWithTasksPagination(int $leadId, int $pageNumb = 1, ?int $activeUserShiftScheduleId = null): array
    {
        $result = [];
        $cache = \Yii::$app->cache;
        $cacheKeyOfSchedules = UserTasksListHelper::generateUserSchedulesListCacheKey($leadId, Auth::id());

        $userShiftSchedules = $cache->getOrSet($cacheKeyOfSchedules, static function () use ($leadId) {
            return UserShiftScheduleQuery::getAllThatHaveTasksByLeadIdAndUserIdAndType($leadId, Auth::id())
                ->with('shiftScheduleEventTask')
                ->all();
        });

        if (!empty($userShiftSchedules)) {
            $userShiftSchedulesNew = [];
            foreach ($userShiftSchedules as $shiftSchedules) {
                $eventId = $shiftSchedules->shiftScheduleEventTask['0']->sset_event_id;
                $userShiftSchedulesNew[$eventId] = $shiftSchedules;
            }

            $activeUserShiftScheduleId = $activeUserShiftScheduleId ?: reset($userShiftSchedulesNew)['uss_id'];
            $activeScheduleData = $activeUserShiftScheduleId ? $userShiftSchedulesNew[$activeUserShiftScheduleId] : reset($userShiftSchedulesNew);

            $cacheKey = UserTasksListHelper::generateUserTasksListCacheKey(
                $leadId,
                Auth::id(),
                $pageNumb,
                $activeUserShiftScheduleId
            );

            if (!$cache->get($cacheKey)) {
                $cacheTag = new TagDependency(['tags' => UserTasksListHelper::getUserTasksListCacheTag($leadId, Auth::id())]);

                $shiftScheduleTasksQuery = UserTaskQuery::getQueryUserIdAndTargetIdAndDateAndTargetObject(
                    Auth::id(),
                    $leadId,
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
            }

            $result = $cache->get($cacheKey);
        }

        return $result;
    }
}
