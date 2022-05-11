<?php

namespace modules\shiftSchedule\src\services;

use common\models\Employee;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use src\auth\Auth;
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
        $userList = [];
        $userGroups = $user->ugsGroups;
        if (!empty($userGroups)) {
            foreach ($userGroups as $group) {
                $userList = array_merge(
                    $userList,
                    array_map(function ($userData) {
                        return $userData->id;
                    }, $group->ugsUsers)
                );
            }
        } else {
            $userList = [$user->id];
        }

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
//                $sstColor = $item->srhSst ? $item->srhSst->sst_color : 'gray';
//                $statusColor = $item->getStatusNameColor(true);

                $dataItem = [
                    'id' => $item->ssr_id,
                    'title' => sprintf(
                        "%s(%s)",
                        $item->getScheduleTypeKey(),
                        $item->getStatusName()
                    ),
                    'description' => sprintf(
                        "%s\r\n(%s), duration: %s (%s)",
                        $item->getScheduleTypeTitle(),
                        $item->ssr_uss_id,
                        $item->getDuration(),
                        $item->getStatusName()
                    ),
                    'start' => date('c', strtotime($item->srhUss->uss_start_utc_dt ?? '')),
                    'end' => date('c', strtotime($item->srhUss->uss_end_utc_dt ?? '')),

                    'resource' => 'us-' . $item->ssr_created_user_id,
                    'extendedProps' => [
                        'icon' => $item->srhSst->sst_icon_class,
//                        'backgroundImage' => 'linear-gradient(45deg, ' . $sstColor . ' 30%, ' . $statusColor . ' 70%)',
                    ],
                    'className' => 'badge-' . $item->getStatusNameColor(),
                ];

                $data[] = $dataItem;
            }
        }
        return $data;
    }
}
