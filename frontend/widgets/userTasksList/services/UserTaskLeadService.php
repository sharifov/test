<?php

namespace frontend\widgets\userTasksList\services;

use frontend\widgets\userTasksList\helpers\UserTasksListHelper;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\taskList\src\entities\userTask\UserTaskQuery;
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

        $userShiftSchedules = $this->getUserShiftSchedules($lead);

        if (!empty($userShiftSchedules)) {
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

                \Yii::info($message, 'info\UserTaskLeadServiceWidget:getSchedulesWithTasksPagination:second');
            }

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

            /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
            if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
                $message = [
                    'leadId' => $lead->id,
                    'employee_id' => $lead->employee_id,
                    'shiftScheduleTasksQuery' => $shiftScheduleTasksQuery,
                    'shiftScheduleTasks' => $shiftScheduleTasks,
                ];

                \Yii::info($message, 'info\UserTaskLeadServiceWidget:getSchedulesWithTasksPagination:third');
            }
        }

        return $result;
    }

    private function getUserShiftSchedules($lead): array
    {
        $query = UserShiftScheduleQuery::getAllThatHaveTasksByLeadIdAndUserIdAndType($lead->id, $lead->employee_id)
            ->with('shiftScheduleEventTask');

        /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
            \Yii::info($query->createCommand()->getRawSql(), 'info\UserTaskLeadServiceWidget:getAllThatHaveTasksByLeadIdAndUserIdAndType:query');
        }

        $result = $query->all();

        /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
            $message = [
                'leadId' => $lead->id,
                'employee_id' => $lead->employee_id,
                'userShiftSchedules' => $result,
            ];

            \Yii::info($message, 'info\UserTaskLeadServiceWidget:getSchedulesWithTasksPagination:first');
        }

        return $result;
    }
}
