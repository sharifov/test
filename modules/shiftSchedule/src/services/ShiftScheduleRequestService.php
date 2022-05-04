<?php

namespace modules\shiftSchedule\src\services;

use common\models\Employee;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use src\auth\Auth;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class ShiftScheduleRequestService
{
    /**
     * Get User List activeQuery
     * @param Employee|null $user
     * @return ActiveQuery
     */
    public static function getUserList(Employee $user = null): ActiveQuery
    {
        if (empty($user)) {
            $user = Auth::user();
        }
        $employee = Employee::find()
            ->select(Employee::tableName() . '.id');

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return $employee;
        }
        $userList = $user->ugsGroups->ugsUsers ?? $user->id;

        return $employee
            ->where(['id' => $userList]);
    }

    /**
     * @param Employee|null $user
     * @return int[]
     */
    public static function getUserListArray(Employee $user = null): array
    {
        $data = self::getUserList($user)->asArray()->all();
        return ArrayHelper::getColumn($data, 'id', false);
    }

    /**
     * @param ActiveQuery $userList
     * @param string $startDate
     * @return array
     */
    public static function getTimelineListByUserList(ActiveQuery $userList, string $startDate): array
    {
        $query = ShiftScheduleRequestSearch::getSearchQuery($userList, null, $startDate);
        return $query->all();
    }

    /**
     * @param ShiftScheduleRequest[] $timelineList
     * @return array
     */
    public static function getCalendarTimelineJsonData(array $timelineList): array
    {
        $data = [];
        if ($timelineList) {
            foreach ($timelineList as $item) {
                $dataItem = [
                    'id' => $item->srh_id,
                    'title' => sprintf(
                        "%s(%s)",
                        $item->getScheduleTypeKey(),
                        $item->getStatusName()
                    ),
                    'description' => Yii::t(
                        'schedule-request',
                        '{scheduleTypeTitle}{new_line}({userShiftScheduleId}), duration: {duration} ({statusName})',
                        [
                            'scheduleTypeTitle' => $item->getScheduleTypeTitle(),
                            'new_line' => "\r\n",
                            'userShiftScheduleId' => $item->srh_uss_id,
                            'duration' => $item->getDuration(),
                            'statusName' => $item->getStatusName(),
                        ]
                    ),
                    'start' => date('c', strtotime($item->srh_start_utc_dt)),
                    'end' => date('c', strtotime($item->srh_end_utc_dt)),

                    'resource' => 'us-' . $item->srh_created_user_id,
                    'extendedProps' => [
                        'icon' => $item->srhSst->sst_icon_class,
                    ],
                    'className' => 'badge-' . $item->getStatusNameColor(),
                    'borderColor' => $item->srhSst ? $item->srhSst->sst_color : 'gray',
                ];

                $data[] = $dataItem;
            }
        }
        return $data;
    }
}
