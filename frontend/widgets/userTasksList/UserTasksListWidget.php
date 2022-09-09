<?php

namespace frontend\widgets\userTasksList;

use src\auth\Auth;
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
        $result = '';
        $userTaskLeadService = new UserTaskLeadService();
        $userSchedulesWithTasks = $userTaskLeadService->getSchedulesWithTasksPagination($this->lead->id, $this->pageNumber, $this->activeShiftScheduleId);

        if (!empty($userSchedulesWithTasks)) {
            $userTimezone = 'UTC';
            $userSchedulesWithTasks = $this->prepareDataForView($userSchedulesWithTasks, $userTimezone);

            $result = $this->render('tasks_list', $userSchedulesWithTasks);
        }

        return $result;
    }

    protected function prepareDataForView($userSchedulesWithTasks, string $userTimeZone)
    {
        $userSchedulesWithTasks['lead'] = $this->lead;
        $userSchedulesWithTasks['userTimeZone'] = $userTimeZone;
        $userSchedulesWithTasks['pjaxUrl'] = Url::to(['lead/pjax-user-tasks-list', 'gid' => $this->lead->gid]);
        $userSchedulesWithTasks['addNoteUrl'] = Url::to(['/task-list/ajax-add-note']);

        // Prepare schedules dates
        $userSchedulesWithTasks['userShiftSchedulesList'] = array_reduce($userSchedulesWithTasks['userShiftSchedules'], function ($carry, $item) use ($userTimeZone) {
            $result = UserShiftScheduleHelper::getDataForTaskList($item, $userTimeZone);
            $startDate = (new \DateTimeImmutable($item['uss_start_utc_dt']))->setTimezone(new \DateTimeZone($userTimeZone))->format('d-M-Y H:i:s');
            $endDate = (new \DateTimeImmutable($item['uss_end_utc_dt']))->setTimezone(new \DateTimeZone($userTimeZone))->format('d-M-Y H:i:s');
            $currentDate = (new \DateTimeImmutable(date('d-M-Y H:i:s')))->setTimezone(new \DateTimeZone($userTimeZone))->format('d-M-Y H:i:s');

            $result['isDayToday'] = DateHelper::isDateInTheRangeOtherTwoDates($currentDate, $startDate, $endDate);

            $carry[$item['sset_event_id']] = $result;
            return $carry;
        }) ?: [];

        return $userSchedulesWithTasks;
    }
}
