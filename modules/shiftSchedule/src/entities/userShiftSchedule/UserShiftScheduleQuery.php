<?php

namespace modules\shiftSchedule\src\entities\userShiftSchedule;

use common\models\UserGroupAssign;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\TimelineCalendarFilter;
use Yii;

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
    public static function getCalendarTimelineListByUser(TimelineCalendarFilter $form): array
    {
        $query = UserShiftSchedule::find()
            ->join('inner join', UserGroupAssign::tableName(), 'ugs_user_id = uss_user_id');
        if ($form->userGroups) {
            $query->andWhere(['ugs_group_id' => $form->userGroups]);
        }
        if ($form->usersIds) {
            $query->andWhere(['ugs_user_id' => $form->usersIds]);
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

        /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS, Hide Soft Deleted Schedule Events */
        if (Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS)) {
            $query->excludeDeleteStatus();
        }

        $query->groupBy(['uss_id']);
        return $query->all();
    }

    /**
     * @param int $userId
     * @param string $startDt
     * @param string $endDt
     * @return array|UserShiftSchedule[]
     */
    public static function getTimelineListByUser(int $userId, string $startDt, string $endDt): array
    {
        return self::getQueryTimelineListByUser($userId, $startDt, $endDt)->all();
    }

    /**
     * @param int $userId
     * @param string $startDt
     * @param string $endDt
     * @return array|UserShiftSchedule[]
     */
    public static function getTimelineListByUserExcludeDeletedEvents(int $userId, string $startDt, string $endDt): array
    {
        return self::getQueryTimelineListByUser($userId, $startDt, $endDt)
            ->excludeDeleteStatus()
            ->all();
    }

    /**
     * @param int $userId
     * @return int
     */
    public static function removeDataByUser(int $userId): int
    {
        return UserShiftSchedule::deleteAll(['uss_user_id' => $userId]);
    }

    /**
     * @param int $userId
     * @param string $minDate
     * @param string $maxDate
     * @param array $statusList
     * @return array|UserShiftSchedule[]
     */
    public static function getUserShiftScheduleDataStats(
        int $userId,
        string $minDate,
        string $maxDate,
        array $statusList = []
    ): array {
        $query = UserShiftSchedule::find()
            ->select(['uss_sst_id', 'uss_year' => 'YEAR(uss_start_utc_dt)',
                'uss_month' => 'MONTH(uss_start_utc_dt)',
                'uss_cnt' => 'COUNT(*)',
                'uss_duration' => 'SUM(uss_duration)',
            ])
            ->where(['uss_user_id' => $userId])
            ->andWhere(['AND',
                ['>=', 'uss_start_utc_dt', $minDate],
                ['<=', 'uss_start_utc_dt', $maxDate]
            ])

            ->groupBy(['uss_sst_id', 'uss_year', 'uss_month'])
            ->asArray();

        if ($statusList) {
            $query->andWhere(['uss_status_id' => $statusList]);
        }

        return $query->all();
    }

    /**
     * @param int $userId
     * @param string $minDate
     * @param string $maxDate
     * @param array $statusList
     * @return array|UserShiftSchedule[]
     */
    public static function getUserShiftScheduleLabelDataStats(
        int $userId,
        string $minDate,
        string $maxDate,
        array $statusList = []
    ): array {
        $query = UserShiftSchedule::find()
            ->select(['stl_key' => 'tla_stl_key', 'uss_year' => 'YEAR(uss_start_utc_dt)',
                'uss_month' => 'MONTH(uss_start_utc_dt)',
                'uss_cnt' => 'COUNT(*)',
                'uss_duration' => 'SUM(uss_duration)',
            ])
            ->alias('uss')
            //->with(['shiftScheduleType'])
            //->innerJoin(ShiftScheduleType::tableName(), 'sst_id=uss.uss_sst_id')
            ->innerJoin(ShiftScheduleTypeLabelAssign::tableName(), 'uss.uss_sst_id = tla_sst_id')
            // ['tla_stl_key', 'tla_sst_id']
            ->where(['uss_user_id' => $userId])
            ->andWhere(['AND',
                ['>=', 'uss_start_utc_dt', $minDate],
                ['<=', 'uss_start_utc_dt', $maxDate]
            ])

            ->groupBy(['tla_stl_key', 'uss_year', 'uss_month'])
            ->asArray();

        if ($statusList) {
            $query->andWhere(['uss_status_id' => $statusList]);
        }

        return $query->all();
    }

    /**
     * @param int $userId
     * @param string $startDt
     * @param string $endDt
     * @param array|null $statusListId
     * @param array|null $subTypeListId
     * @return array
     */
    public static function getUserEventIdList(
        int $userId,
        string $startDt,
        string $endDt,
        ?array $statusListId = [],
        ?array $subTypeListId = []
    ): array {

        if ($statusListId === null) {
            $statusListId = [UserShiftSchedule::STATUS_APPROVED, UserShiftSchedule::STATUS_DONE];
        }

        if ($subTypeListId === null) {
            $subTypeListId = [ShiftScheduleType::SUBTYPE_WORK_TIME, ShiftScheduleType::SUBTYPE_HOLIDAY];
        }

        return self::getExistEventIdList($userId, $startDt, $endDt, $statusListId, $subTypeListId);
    }

    /**
     * @param int $userId
     * @param string $startDateTime
     * @param string $endDateTime
     * @param array|null $statusListId
     * @param array|null $subTypeListId
     * @return array
     */
    public static function getExistEventIdList(
        int $userId,
        string $startDateTime,
        string $endDateTime,
        ?array $statusListId = [],
        ?array $subTypeListId = []
    ): array {
        $query = UserShiftSchedule::find();
        $query->alias('uss');
        $query->select(['uss.uss_id']);
        $query->where(['uss.uss_user_id' => $userId]);

        if (!empty($statusListId)) {
            $query->andWhere(['uss.uss_status_id' => $statusListId]);
        }

        if (!empty($subTypeListId)) {
            $query->innerJoin(ShiftScheduleType::tableName() . ' AS sst', 'sst.sst_id = uss.uss_sst_id');
            $query->andWhere(['sst.sst_subtype_id' => $subTypeListId]);
        }

        if (!empty($startDateTime) && !empty($endDateTime)) {
            $query->andWhere([
                'OR',
                ['between', 'uss.uss_start_utc_dt', $startDateTime, $endDateTime],
                ['between', 'uss.uss_end_utc_dt', $startDateTime, $endDateTime],
                [
                    'AND',
                    ['>=', 'uss.uss_start_utc_dt', $startDateTime],
                    ['<=', 'uss.uss_end_utc_dt', $endDateTime]
                ],
                [
                    'AND',
                    ['<=', 'uss.uss_start_utc_dt', $startDateTime],
                    ['>=', 'uss.uss_end_utc_dt', $endDateTime]
                ]
            ]);
        }

        return $query->column();
    }

    /**
     * @param UserShiftSchedule[] $timelineList
     * @param string $userTimeZone
     * @return array
     */
    public static function getCalendarTimelineJsonData(array $timelineList, string $userTimeZone): array
    {
        $data = [];
        if ($timelineList) {
            foreach ($timelineList as $item) {
                $dataItem = [
                    'id' => $item->uss_id,
                    'title' => $item->getScheduleTypeKey(),
                    'description' => $item->getScheduleTypeTitle() . "\r\n" . '(' . $item->uss_id . ')' . ', duration: ' .
                        Yii::$app->formatter->asDuration($item->uss_duration * 60),
                    'start' => Yii::$app->formatter->asDateTimeByUserTimezone(
                        strtotime($item->uss_start_utc_dt ?? ''),
                        $userTimeZone,
                        'php: c'
                    ),
                    'end' => Yii::$app->formatter->asDateTimeByUserTimezone(
                        strtotime($item->uss_end_utc_dt ?? ''),
                        $userTimeZone,
                        'php: c'
                    ),
                    'color' => $item->shiftScheduleType ? $item->shiftScheduleType->sst_color : 'gray',
                    'display' => 'block',
                    'extendedProps' => [
                        'icon' => $item->shiftScheduleType->sst_icon_class ?? '',
                    ]
                ];

                if (
                    !in_array($item->uss_status_id, [
                        UserShiftSchedule::STATUS_DONE,
                        UserShiftSchedule::STATUS_APPROVED
                    ], true)
                ) {
                    $dataItem['borderColor'] = '#000000';
                    $dataItem['title'] .= ' (' . $item->getStatusName() . ')';
                    $dataItem['description'] .= ' (' . $item->getStatusName() . ')';
                }

                $data[] = $dataItem;
            }
        }
        return $data;
    }

    /**
     * @param int $userId
     * @param string $startDt
     * @param string $endDt
     * @return Scopes
     */
    private static function getQueryTimelineListByUser(int $userId, string $startDt, string $endDt): Scopes
    {
        return UserShiftSchedule::find()
            ->where(['uss_user_id' => $userId])
            ->andWhere(['AND',
                ['>=', 'uss_start_utc_dt', date('Y-m-d H:i:s', strtotime($startDt))],
                ['<=', 'uss_start_utc_dt', date('Y-m-d H:i:s', strtotime($endDt))]
            ]);
    }
}
