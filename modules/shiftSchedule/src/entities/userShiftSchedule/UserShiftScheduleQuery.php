<?php

namespace modules\shiftSchedule\src\entities\userShiftSchedule;

use common\models\UserGroup;
use common\models\UserGroupAssign;

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
     * @param string $startDt
     * @param string $endDt
     * @param array $groupIds
     * @return UserShiftSchedule[]
     */
    public static function getTimelineListByUser(string $startDt, string $endDt, array $groupIds): array
    {
        return UserShiftSchedule::find()
            ->join('inner join', UserGroupAssign::tableName(), 'ugs_user_id = uss_user_id')
            ->andWhere(['ugs_group_id' => $groupIds])
            ->andWhere(['AND',
                ['>=', 'uss_start_utc_dt', date('Y-m-d H:i:s', strtotime($startDt))],
                ['<=', 'uss_start_utc_dt', date('Y-m-d H:i:s', strtotime($endDt))]
            ])
            ->all();
    }
}
