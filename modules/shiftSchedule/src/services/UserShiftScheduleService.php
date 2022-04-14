<?php

namespace modules\shiftSchedule\src\services;

use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use Yii;

class UserShiftScheduleService
{
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
     * @param string $startDt
     * @param string $endDt
     * @return array|UserShiftSchedule[]
     */
    public static function getTimelineListByUser(int $userId, string $startDt, string $endDt): array
    {
        $timelineList = UserShiftSchedule::find()
            ->where(['uss_user_id' => $userId])
            ->andWhere(['AND',
                ['>=', 'uss_start_utc_dt', date('Y-m-d H:i:s', strtotime($startDt))],
                ['<=', 'uss_start_utc_dt', date('Y-m-d H:i:s', strtotime($endDt))]
            ])
            ->all();

        return $timelineList;
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
     * @param UserShiftSchedule[] $timelineList
     * @return array
     */
    public static function getCalendarTimelineJsonData(array $timelineList): array
    {
        $data = [];
        if ($timelineList) {
            foreach ($timelineList as $item) {
                $dataItem = [
                    'id' => $item->uss_id,
                    //groupId: '999',
                    'title' => $item->getScheduleTypeKey() . '-' . $item->uss_id,
                        //. ' - ' . date('d H:i', strtotime($item->uss_start_utc_dt)),
                    'description' => $item->getScheduleTypeTitle() . "\r\n" . ', duration: ' .
                        Yii::$app->formatter->asDuration($item->uss_duration * 60),
                    //. "\r\n" . $item->uss_description,
                    'start' => date('c', strtotime($item->uss_start_utc_dt)),
                    'end' => date('c', strtotime($item->uss_end_utc_dt)),
                    'color' => $item->shiftScheduleType ? $item->shiftScheduleType->sst_color : 'gray',

                    'display' => 'block', // 'list-item' , 'background'
                    //'textColor' => 'black' // an option!
                    'extendedProps' => [
                        'icon' => $item->shiftScheduleType->sst_icon_class,
                    ]
                ];

                if (
                    !in_array($item->uss_status_id, [UserShiftSchedule::STATUS_DONE,
                    UserShiftSchedule::STATUS_APPROVED])
                ) {
                    $dataItem['borderColor'] = '#000000';
                    $dataItem['title'] .= ' (' . $item->getStatusName() . ')';
                    $dataItem['description'] .= ' (' . $item->getStatusName() . ')';
                    //$data[$item->uss_id]['extendedProps']['icon'] = 'rgb(255,0,0)';
                }

                $data[] = $dataItem;
            }
        }
        return $data;
    }

    /**
     * @param int $userId
     * @param int $days
     * @return int
     * @throws \Exception
     */
    public static function generateExampleDataByUser(int $userId, int $days = 90): int
    {
        $statuses = array_keys(UserShiftSchedule::getTypeList());
        $shiftList = array_keys(Shift::getList());
        $shiftRuleList = array_keys(ShiftScheduleRule::getList());
        $shiftTypeList = array_keys(ShiftScheduleType::getList());

        $minutes = [0, 10, 20, 30, 40];

        //VarDumper::dump($statuses, 10, true); exit;

        $cnt = 0;

        for ($i = 1; $i <= $days; $i++) {
            $hour = random_int(12, 22);
            $min = $minutes[random_int(1, count($minutes)) - 1];
            $timeStart = mktime($hour, $min, 0, date("m") - 1, $i, date("Y"));
            $duration = random_int(2, 8) * 60 + $minutes[random_int(1, count($minutes)) - 1];
            $timeEnd = mktime($hour, $duration + $min, 0, date("m") - 1, $i, date("Y"));


            $statusId = $statuses[random_int(0, count($statuses) - 1)];
            $shiftId = $shiftList[random_int(0, count($shiftList) - 1)];
            $shiftRuleId = $shiftRuleList[random_int(0, count($shiftRuleList) - 1)];
            $shiftTypeId = $shiftTypeList[random_int(0, count($shiftTypeList) - 1)];

            $tl = new UserShiftSchedule();
            $tl->uss_user_id = $userId;
            $tl->uss_sst_id = $shiftTypeId;
            $tl->uss_type_id = random_int(1, 2);
            $tl->uss_start_utc_dt = date('Y-m-d H:i:s', $timeStart);
            $tl->uss_end_utc_dt = date('Y-m-d H:i:s', $timeEnd);
            $tl->uss_duration = $duration;
            $tl->uss_ssr_id = $shiftRuleId;
            $tl->uss_shift_id = $shiftId;
            $tl->uss_status_id = $statusId;
            $tl->uss_description = 'Description ' . $tl->uss_start_utc_dt;
            if ($tl->save()) {
                $cnt++;
            }
        }
        return $cnt;
    }
}
