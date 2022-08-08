<?php

namespace modules\shiftSchedule\src\entities\userShiftSchedule;

use common\models\UserGroupAssign;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\TimelineCalendarFilter;
use Yii;
use yii\base\DynamicModel;
use yii\db\Expression;

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
        $query = UserShiftSchedule::find();
        if ($form->userGroups) {
            $query->andWhere(['uss_user_id' => UserGroupAssign::find()->select('ugs_user_id')->andWhere(['ugs_group_id' => $form->userGroups])]);
        }
        if ($form->usersIds) {
            $query->andWhere(['uss_user_id' => $form->usersIds]);
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

        if (($form->startDateTime && $form->startDateTimeCondition) || ($form->endDateTime && $form->endDateTimeCondition)) {
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
        } else {
            $startDateTime = date('Y-m-d H:i', strtotime($form->startDate));
            $endDateTime = date('Y-m-d H:i', strtotime($form->endDate));

            $query->andWhere([
                'OR',
                ['between', 'uss_start_utc_dt', $startDateTime, $endDateTime],
                ['between', 'uss_end_utc_dt', $startDateTime, $endDateTime],
                [
                    'AND',
                    ['>=', 'uss_start_utc_dt', $startDateTime],
                    ['<=', 'uss_end_utc_dt', $endDateTime]
                ],
                [
                    'AND',
                    ['<=', 'uss_start_utc_dt', $startDateTime],
                    ['>=', 'uss_end_utc_dt', $endDateTime]
                ]
            ]);
        }
        if ($form->shift) {
            $query->andWhere(['uss_shift_id' => $form->shift]);
        }

        /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS, Hide Soft Deleted Schedule Events */
        if (Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS)) {
            $query->excludeDeleteStatus();
        }


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
     * @param int $userId
     * @param string $startDateTime
     * @param string $endDateTime
     * @param array|null $statusListId
     * @param array|null $subTypeListId
     * @return array|UserShiftSchedule[]
     */
    public static function getExistEventList(
        int $userId,
        string $startDateTime,
        string $endDateTime,
        ?array $statusListId = [],
        ?array $subTypeListId = []
    ): array {
        $query = UserShiftSchedule::find();
        $query->alias('uss');
        // $query->select(['uss.uss_id']);
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
        $query->orderBy(['uss.uss_id' => SORT_ASC]);
        return $query->all();
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
        $startDateTime = date('Y-m-d H:i', strtotime($startDt));
        $endDateTime = date('Y-m-d H:i', strtotime($endDt));

        return UserShiftSchedule::find()
            ->andWhere([
                'OR',
                ['between', 'uss_start_utc_dt', $startDateTime, $endDateTime],
                ['between', 'uss_end_utc_dt', $startDateTime, $endDateTime],
                [
                    'AND',
                    ['>=', 'uss_start_utc_dt', $startDateTime],
                    ['<=', 'uss_end_utc_dt', $endDateTime]
                ],
                [
                    'AND',
                    ['<=', 'uss_start_utc_dt', $startDateTime],
                    ['>=', 'uss_end_utc_dt', $endDateTime]
                ]
            ])
            ->andWhere(['uss_user_id' => $userId]);
    }

    /**
     * @return UserShiftSchedule[]
     */
    public static function getPendingListWithIntersectionByWTAndWTR(): array
    {
        $curTime = date('Y-m-d H:i:s');
        $ussTableName = UserShiftSchedule::tableName();
        $ssrTableName = ShiftScheduleRequest::tableName();

        return UserShiftSchedule::find()
            ->alias('ussPending')
            ->where(['ussPending.uss_status_id' => UserShiftSchedule::STATUS_PENDING])
            ->andWhere(['AND',
                ['<=', 'ussPending.uss_start_utc_dt', $curTime],
            ])
            ->andWhere(['AND',
                ['>=', 'ussPending.uss_end_utc_dt', $curTime],
            ])
            ->rightJoin(
                "{$ussTableName} AS ussDone",
                "ussPending.uss_user_id = ussDone.uss_user_id 
                AND ussDone.uss_start_utc_dt <= :curTime 
                AND ussDone.uss_end_utc_dt >= :curTime",
                [
                    'curTime' => $curTime,
                ]
            )
            ->andOnCondition([
                'IN',
                'ussDone.uss_status_id',
                [UserShiftSchedule::STATUS_DONE, UserShiftSchedule::STATUS_APPROVED]
            ])
            ->andOnCondition([
                'IN',
                'ussDone.uss_sst_id',
                ShiftScheduleType::getIdListByKeys([
                    ShiftScheduleType::TYPE_KEY_WT, ShiftScheduleType::TYPE_KEY_WTR
                ])
            ])
            ->rightJoin($ssrTableName, "ussPending.uss_id = {$ssrTableName}.ssr_uss_id")
            ->all();
    }

    private static function getQueryForNextShiftsByUserId(
        int $userId,
        \DateTimeImmutable $startDt,
        ?\DateTimeImmutable $userTaskEndDt = null,
        array $status = [UserShiftSchedule::STATUS_APPROVED, UserShiftSchedule::STATUS_DONE],
        array $shiftScheduleType = [ShiftScheduleType::SUBTYPE_WORK_TIME]
    ): Scopes {
        $query = UserShiftSchedule::find()
            ->alias('user_shift_schedule')
            ->select('user_shift_schedule.*')
            ->innerJoin(
                ShiftScheduleType::tableName() . ' AS shift_schedule_type',
                'shift_schedule_type.sst_id = user_shift_schedule.uss_sst_id',
            )
            ->where(['uss_user_id' => $userId])
            ->andWhere(['IN', 'uss_status_id', $status])
            ->andWhere(['IN', 'shift_schedule_type.sst_subtype_id', $shiftScheduleType])
        ;

        if ($userTaskEndDt) {
            $startDateTime = $startDt->format('Y-m-d H:i:s');
            $endDateTime = $userTaskEndDt->format('Y-m-d H:i:s');
            $query->andWhere([
                'OR',
                ['BETWEEN', 'uss_start_utc_dt', $startDateTime, $endDateTime],
                ['BETWEEN', 'uss_end_utc_dt', $startDateTime, $endDateTime],
                [
                    'AND',
                    ['>=', 'uss_start_utc_dt', $startDateTime],
                    ['<=', 'uss_end_utc_dt', $endDateTime]
                ],
                [
                    'AND',
                    ['<=', 'uss_start_utc_dt', $startDateTime],
                    ['>=', 'uss_end_utc_dt', $endDateTime]
                ]
            ]);
        } else {
            $query->andWhere(['>', 'uss_end_utc_dt', $startDt->format('Y-m-d H:i:s')]);
        }

        $query->orderBy(['uss_end_utc_dt' => SORT_ASC]);

        return $query;
    }

    /**
     * @param int $userId
     * @param \DateTimeImmutable $startDt
     * @param array $status
     * @param array $shiftScheduleType
     * @return UserShiftSchedule[]|null
     */
    public static function getAllFromStartDateByUserId(
        int $userId,
        \DateTimeImmutable $startDt,
        ?\DateTimeImmutable $userTaskEndDt = null,
        array $status = [UserShiftSchedule::STATUS_APPROVED, UserShiftSchedule::STATUS_DONE],
        array $shiftScheduleType = [ShiftScheduleType::SUBTYPE_WORK_TIME]
    ): ?array {
        return self::getQueryForNextShiftsByUserId($userId, $startDt, $userTaskEndDt, $status, $shiftScheduleType)->all();
    }
}
