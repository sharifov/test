<?php

namespace modules\shiftSchedule\src\entities\userShiftSchedule;

use common\models\UserGroup;
use common\models\UserGroupAssign;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\TimelineCalendarFilter;

class UserShiftScheduleQuery
{
    public static function manualBatchInsert(
        array $userIds,
        \DateTimeImmutable $startDateTime,
        \DateTimeImmutable $endDateTime,
        int $duration,
        int $status,
        int $scheduleType,
        ?string $description,
        int $createdUserId
    ): int {
        $insertData = [];
        $start = $startDateTime->format('Y-m-d H:i:s');
        $end = $endDateTime->format('Y-m-d H:i:s');
        $year = $startDateTime->format('Y');
        $month = $startDateTime->format('m');
        $createdDt = $updatedDt = date('Y-m-d H:i:s');
        foreach ($userIds as $userId) {
            $insertData[] = [
                $userId,
                $description,
                $start,
                $end,
                $duration,
                $status,
                $scheduleType,
                $year,
                $month,
                $createdUserId,
                $createdDt,
                $updatedDt
            ];
        }
        $insertedRows = UserShiftSchedule::getDb()->createCommand()->batchInsert(UserShiftSchedule::tableName(), [
            'uss_user_id',
            'uss_description',
            'uss_start_utc_dt',
            'uss_end_utc_dt',
            'uss_duration',
            'uss_status_id',
            'uss_type_id',
            'uss_year_start',
            'uss_month_start',
            'uss_created_user_id',
            'uss_created_dt',
            'uss_updated_dt'
        ], $insertData)->execute();
    }

    /**
     * @param TimelineCalendarFilter $form
     * @return UserShiftSchedule[]
     */
    public static function getTimelineListByUser(TimelineCalendarFilter $form): array
    {
        $query = UserShiftSchedule::find()
            ->join('inner join', UserGroupAssign::tableName(), 'ugs_user_id = uss_user_id')
            ->andWhere(['ugs_group_id' => $form->userGroups]);
        if ($form->users) {
            $query->andWhere(['ugs_user_id' => $form->users]);
        }
        if ($form->statuses) {
            $query->andWhere(['uss_status_id' => $form->statuses]);
        }
        if ($form->scheduleTypes) {
            $query->andWhere(['uss_sst_id' => $form->scheduleTypes]);
        }
        if ($form->duration) {
            $query->andWhere(['uss_duration' => $form->duration]);
        }
        if ($form->startDateTime && $form->startDateTimeCondition) {
            $query->andWhere([$form->getStartDateTimeConditionOperator(), 'uss_start_utc_dt', date('Y-m-d H:i', strtotime($form->startDateTime))]);
        } else {
            $query->andWhere(['>=', 'uss_start_utc_dt', date('Y-m-d H:i:s', strtotime($form->startDate))]);
        }
        if ($form->endDateTime && $form->endDateTimeCondition) {
            $query->andWhere([$form->getEndDateTimeConditionOperator(), 'uss_end_utc_dt', date('Y-m-d H:i', strtotime($form->endDateTime))]);
        } else {
            $query->andWhere(['<=', 'uss_start_utc_dt', date('Y-m-d 23:59:59', strtotime($form->endDate))]);
        }
        if ($form->shift) {
            $query->andWhere(['uss_shift_id' => $form->shift]);
        }

        return $query->all();
    }
}
