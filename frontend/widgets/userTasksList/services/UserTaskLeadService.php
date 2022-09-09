<?php

namespace frontend\widgets\userTasksList\services;

use frontend\widgets\userTasksList\helpers\UserTasksListHelper;
use modules\shiftSchedule\src\entities\userShiftSchedule\{
    UserShiftSchedule,
    UserShiftScheduleRepository
};
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
            $userShiftScheduleRepository = new UserShiftScheduleRepository(new UserShiftSchedule());
            return $userShiftScheduleRepository->getAllThatHaveTasksByLeadIdAndUserIdAndType($leadId, Auth::id());
        });

        if (!empty($userShiftSchedules)) {
            $activeUserShiftScheduleId = $activeUserShiftScheduleId ?: reset($userShiftSchedules)['uss_id'];
            $activeScheduleData = $activeUserShiftScheduleId ? $userShiftSchedules[$activeUserShiftScheduleId] : reset($userShiftSchedules);

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
                    'userShiftSchedules' => $userShiftSchedules,
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
