<?php

namespace modules\lead\src\services;

use modules\taskList\src\entities\shiftScheduleEventTask\repository\ShiftScheduleEventTaskRepository;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use modules\taskList\src\entities\userTask\repository\UserTaskRepository;
use modules\taskList\src\entities\userTask\UserTask;
use src\helpers\DateHelper;
use src\helpers\ErrorsToStringHelper;

abstract class LeadTaskAssignService implements LeadTaskAssignInterface
{
    public function createShiftScheduleEventTask(array $userShiftSchedules, UserTask $userTask, \DateTimeImmutable $dtNowWithDelay, ?int $duration)
    {
        $userTaskListEndDate = null;
        foreach ($userShiftSchedules as $userShiftSchedule) {
            $shiftScheduleEventTask = ShiftScheduleEventTask::create(
                $userShiftSchedule->uss_id,
                $userTask->ut_id
            );

            if (!$shiftScheduleEventTask->validate()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($shiftScheduleEventTask, ' '));
            }

            (new ShiftScheduleEventTaskRepository($shiftScheduleEventTask))->save();

            if ($duration !== null) {
                if (DateHelper::toFormatByUTC($userShiftSchedule->uss_start_utc_dt) === $dtNowWithDelay->format('Y-m-d')) {
                    $leftMinutes = DateHelper::getDifferentInMinutesByDatesUTC($dtNowWithDelay->format('Y-m-d H:i:s'), $userShiftSchedule->uss_end_utc_dt);
                    $calculatedDuration = $duration - $leftMinutes;
                } else {
                    $calculatedDuration = $duration - $userShiftSchedule->uss_duration;
                }

                if ($calculatedDuration <= 0) {
                    $userTaskListEndDate = DateHelper::getDateTimeWithAddedMinutesUTC($userShiftSchedule->uss_end_utc_dt, $duration);
                    break;
                }

                $duration = $calculatedDuration;
            }
        }

        if ($userTaskListEndDate !== null) {
            $userTask->ut_end_dt = $userTaskListEndDate;

            if (!$userTask->validate()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($userTask, ' '));
            }

            (new UserTaskRepository($userTask))->save();
        }
    }
}
