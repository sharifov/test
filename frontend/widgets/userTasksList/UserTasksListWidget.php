<?php

namespace frontend\widgets\userTasksList;

use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\base\Widget;
use yii\helpers\Url;
use common\models\Lead;
use src\helpers\DateHelper;
use frontend\widgets\userTasksList\services\UserTaskLeadService;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;

/**
 * @property Lead $lead
 * @property int $pageNumber
 * @property int|null $activeShiftScheduleId
 */
class UserTasksListWidget extends Widget
{
    public Lead $lead;
    public int $pageNumber = 1;
    public ?int $activeShiftScheduleId = null;

    /**
     * @return string
     */
    public function run(): string
    {
        $userTaskLeadService = new UserTaskLeadService();
        $this->lead->employee_id = null;
        $userSchedulesWithTasks = $userTaskLeadService->getSchedulesWithTasksPagination($this->lead, $this->pageNumber, $this->activeShiftScheduleId);

        /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
            $message = [
                'leadId' => $this->lead->id,
                'employee_id' => $this->lead->employee_id,
                'userSchedulesWithTasks' => $userSchedulesWithTasks,
            ];

            \Yii::info($message, 'info\UserTasksListWidget:run:first');
        }

        $userTimezone = 'UTC';
        $userSchedulesWithTasks = $this->prepareDataForView($userSchedulesWithTasks, $userTimezone);

        /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE, Log new task list on lead page */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE)) {
            $message = [
                'leadId' => $this->lead->id,
                'employee_id' => $this->lead->employee_id,
                'userTimezone' => $userTimezone,
                'userSchedulesWithTasks' => $userSchedulesWithTasks,
            ];

            \Yii::info($message, 'info\UserTasksListWidget:run:second');
        }

        $result = $this->render('tasks_list', $userSchedulesWithTasks);
        return $result;
    }

    protected function prepareDataForView($userSchedulesWithTasks, string $userTimeZone)
    {
        $userSchedulesWithTasks['lead'] = $this->lead;
        $userSchedulesWithTasks['userTimeZone'] = $userTimeZone;
        $userSchedulesWithTasks['pjaxUrl'] = Url::to(['lead/pjax-user-tasks-list', 'gid' => $this->lead->gid]);
        $userSchedulesWithTasks['addNoteUrl'] = Url::to(['/task-list/ajax-add-note']);
        $userSchedulesWithTasks['userShiftSchedulesList'] = [];

        // Prepare schedules dates
        if (!empty($userSchedulesWithTasks['userShiftSchedules'])) {
            $userSchedulesWithTasks['userShiftSchedulesList'] = array_reduce($userSchedulesWithTasks['userShiftSchedules'], function ($carry, $item) use ($userTimeZone) {
                /** @var UserShiftSchedule $item */
                $newItem = $item->toArray();
                $newItem['sset_event_id'] = $item->shiftScheduleEventTask['0']->sset_event_id;

                $result = UserShiftScheduleHelper::getDataForTaskList($newItem, $userTimeZone);
                $startDate = (new \DateTimeImmutable($item->uss_start_utc_dt))->setTimezone(new \DateTimeZone($userTimeZone))->format('d-M-Y H:i:s');
                $endDate = (new \DateTimeImmutable($item->uss_end_utc_dt))->setTimezone(new \DateTimeZone($userTimeZone))->format('d-M-Y H:i:s');
                $currentDate = (new \DateTimeImmutable(date('d-M-Y H:i:s')))->setTimezone(new \DateTimeZone($userTimeZone))->format('d-M-Y H:i:s');

                $result['isDayToday'] = DateHelper::isDateInTheRangeOtherTwoDates($currentDate, $startDate, $endDate);

                $carry[$newItem['sset_event_id']] = $result;
                return $carry;
            }) ?: [];
        }

        return $userSchedulesWithTasks;
    }
}
