<?php

namespace modules\shiftSchedule\src\helpers;

use modules\shiftSchedule\src\abac\dto\ShiftAbacDto;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use common\models\Employee;
use common\models\UserGroup;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use src\access\EmployeeGroupAccess;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use src\auth\Auth;

class UserShiftScheduleHelper
{
    private static array $scheduleTypeList = [];
    private static array $statusList = [];

    /**
     * @param UserShiftSchedule[] $data
     * @return array
     */
    public static function getCalendarEventsData(array $eventList): array
    {
        $data = [];
        if ($eventList) {
            foreach ($eventList as $item) {
                $data[] = self::getDataForCalendar($item);
            }
        }
        return $data;
    }

    public static function getDataForCalendar(UserShiftSchedule $event)
    {
        $dataItem = [
            'id' => $event->uss_id,
            'title' => $event->getScheduleTypeKey(), // . '-' . $item->uss_id,
            'description' => $event->getScheduleTypeTitle() . "\r\n" . '(' . $event->uss_id . ')' . ', duration: ' .
                \Yii::$app->formatter->asDuration($event->uss_duration * 60),
            'start' => date('c', strtotime($event->uss_start_utc_dt)),
            'end' => date('c', strtotime($event->uss_end_utc_dt)),
            'color' => $event->shiftScheduleType ? $event->shiftScheduleType->sst_color : 'gray',
            'resource' => 'us-' . $event->uss_user_id,
            //'textColor' => 'black' // an option!
            'extendedProps' => [
                'icon' => $event->shiftScheduleType->sst_icon_class ?? '',
            ],
            'status' => $event->statusName,
            'username' => $event->user->username
        ];

        if (!in_array($event->uss_status_id, [UserShiftSchedule::STATUS_DONE, UserShiftSchedule::STATUS_APPROVED], true)) {
            $dataItem['borderColor'] = '#000000';
            $dataItem['title'] .= ' (' . $event->getStatusName() . ')';
            $dataItem['description'] .= ' (' . $event->getStatusName() . ')';
        }

        return $dataItem;
    }

    /**
     * Getting available schedule type list based on abac policies
     * @return array
     */
    public static function getAvailableScheduleTypeList(): array
    {
        if (empty($shiftScheduleTypeList = self::$scheduleTypeList)) {
            foreach (ShiftScheduleType::getList(true) as $typeId => $typeName) {
                $dto = new ShiftAbacDto();
                $dto->setScheduleType((int)$typeId);
                if (Yii::$app->abac->can($dto, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_ACCESS)) {
                    $shiftScheduleTypeList[$typeId] = $typeName;
                }
            }
        }
        return $shiftScheduleTypeList;
    }

    /**
     * Getting available status list based on abac policies
     * @return array
     */
    public static function getAvailableStatusList(): array
    {
        if (empty($statusList = self::$statusList)) {
            foreach (UserShiftSchedule::getStatusList() as $statusId => $statusName) {
                $dto = new ShiftAbacDto();
                $dto->setStatus((int)$statusId);
                if (Yii::$app->abac->can($dto, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_ACCESS)) {
                    $statusList[$statusId] = $statusName;
                }
            }
        }
        return $statusList;
    }

    /**
     * @param UserGroup[] $userGroups
     * @param array $usersIds
     * @return array
     */
    public static function prepareResourcesForTimelineCalendar(array $userGroups, array $usersIds = []): array
    {
        $resourceList = [];
        $groupIds = [];

        foreach ($userGroups as $key => $group) {
            $childrenIds = [];
            $resource = [
                'id' => 'ug-' . $group->ug_id,
                'name' => '<i class="fa fa-users"></i> ' . $group->ug_name,
                'color' => '#1dab2f',
                'title' => $group->ug_key,
                'collapsed' => $key !== 0,
                'isGroup' => true,
                'description' => '',
                'icons' => [],
                'childrenIds' => []
            ];

            $users = Employee::find()
                ->joinWith(['userGroupAssigns'])
                ->where(['ugs_group_id' => $group->ug_id])
                ->andWhere(['<>', 'status', Employee::STATUS_DELETED])
                ->orderBy(['username' => SORT_ASC])
                ->andFilterWhere(['id' => $usersIds])
                ->all();
            if ($users) {
                $userList = [];
                foreach ($users as $i => $user) {
                    $userResourceId = 'us-' . $user->id;
                    $userList[] = [
                        'id' => $userResourceId,
                        'name' => '#' . ($i + 1) . ' <i class="fa fa-user"></i> ' . $user->username,
                        'color' => '#1dab2f',
                        'title' => '(' . $user->id . ') ' . $user->email,
                        'isGroup' => false,
                        'icons' => [
                            Html::a('<i class="fa fa-calendar"></i>', Url::to(['/shift-schedule/user', 'id' => $user->id]), [
                                'title' => 'User Shift Calendar',
                                'target' => '_blank'
                            ]),
                            Html::a('<i class="fa fa fa-user-plus">', Url::to(['/shift/user-shift-assign/index', 'UserShiftAssignListSearch[userId]' => $user->id]), [
                                'title' => 'User Shift Assign',
                                'target' => '_blank'
                            ])
                        ],
                        'description' => ''
                    ];
                    $childrenIds[] = $userResourceId;
                }
                $resource['name'] .= ' <sup>' . count($userList) . '</sup>';
                $resource['children'] = $userList;
                $resource['childrenIds'] = $childrenIds;
            }
            $resourceList[] = $resource;
            $groupIds[] = $group->ug_id;
        }
        return [$resourceList, $groupIds];
    }

    public static function getDurationForDates(UserShiftSchedule $userShiftSchedule): int
    {
        $timezone = Auth::user()->timezone ?: null;

        $startDateTime = new \DateTimeImmutable($userShiftSchedule->uss_start_utc_dt, $timezone ? new \DateTimeZone($timezone) : null);
        $startDateTime = $startDateTime->setTimezone(new \DateTimeZone('UTC'));
        $endDateTime = new \DateTimeImmutable($userShiftSchedule->uss_end_utc_dt, $timezone ? new \DateTimeZone($timezone) : null);
        $endDateTime = $endDateTime->setTimezone(new \DateTimeZone('UTC'));
        $interval = $startDateTime->diff($endDateTime);

        return $interval->i + ($interval->h * 60);
    }

    /**
     * Gett request status list after abac processing
     * @return array
     */
    public static function getAvailableRequestStatusList(): array
    {
        $statusList = [];
        foreach (ShiftScheduleRequest::getStatusList() as $statusId => $statusName) {
            $dto = new ShiftAbacDto();
            $dto->setRequestStatus((int)$statusId);
            if (Yii::$app->abac->can($dto, ShiftAbacObject::OBJ_USER_SHIFT_REQUEST_EVENT, ShiftAbacObject::ACTION_ACCESS)) {
                $statusList[$statusId] = $statusName;
            }
        }

        return $statusList;
    }

    /**
     * Apply abac policy on users list and return processed users
     * @param int $userId
     * @return array
     */
    public static function getSupervisionByUsers(int $userId): array
    {
        $userList = [];
        $userGroupAssignList = EmployeeGroupAccess::usersIdsInCommonGroupsSubQuery($userId)->all();
        foreach ($userGroupAssignList as $user) {
            if (empty($user->ugsUser) || $user->ugsUser->id === $userId) {
                continue;
            }
            if (\Yii::$app->abac->can(null, ShiftAbacObject::ACT_SEND_SUPERVISION_NOTIFICATION, ShiftAbacObject::ACTION_ACCESS, $user->ugsUser)) {
                $userList[] = $user->ugsUser;
            }
        }
        return $userList;
    }
}
