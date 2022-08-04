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
use yii\helpers\ArrayHelper;
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
        /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS, Hide Soft Deleted Schedule Events */
        $canHideSoftDeleted = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS);

        $data = [];
        if ($eventList) {
            foreach ($eventList as $item) {
                if (!($item->isDeletedStatus() && $canHideSoftDeleted)) {
                    $data[] = self::getDataForCalendar($item);
                }
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
            'resource' => $event->uss_user_id,
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
     * @param array $userGroups
     * @param array $collapsedResources
     * @return array
     */
    public static function prepareResourcesForTimelineCalendar(array $userGroups, array $collapsedResources = []): array
    {
        $resourceList = [];
        $groupIds = [];

        $userCount = 0;
        foreach ($userGroups as $key => $group) {
            if (!isset($resourceList[$group['ug_id']])) {
                $collapsed = $key !== 0;
                if (!empty($collapsedResources)) {
                    if (in_array($group['ug_id'], $collapsedResources, true)) {
                        $collapsed = false;
                    } else {
                        $collapsed = true;
                    }
                }
                $resourceList[$group['ug_id']] = self::mainResource($group, $collapsed);
                $groupIds[] = 'ug-' . $group['ug_id'];
                $userCount = 0;
            }

            $resourceList[$group['ug_id']]['children'][] = self::childResource($group, $userCount++);
            $resourceList[$group['ug_id']]['childrenIds'][] = $group['ugs_user_id'];
        }
        return [$resourceList, $groupIds];
    }

    private static function mainResource(array $data, bool $collapsed): array
    {
        return [
            'id' => 'ug-' . $data['ug_id'],
            'name' => '<i class="fa fa-users"></i> ' . $data['ug_name'],
            'color' => '#1dab2f',
            'title' => $data['ug_key'],
            'collapsed' => $collapsed,
            'isGroup' => true,
            'description' => '',
            'icons' => [],
            'childrenIds' => [],
            'children' => [],
            'mainId' => $data['ug_id']
        ];
    }

    private static function childResource(array $data, int $itemNum): array
    {
        return [
            'id' => $data['ugs_user_id'],
            'name' => '#' . ($itemNum + 1) . ' <i class="fa fa-user"></i> ' . $data['username'],
            'color' => '#1dab2f',
            'title' => '(' . $data['ugs_user_id'] . ') ' . $data['email'],
            'isGroup' => false,
            'icons' => [
                Html::a('<i class="fa fa-calendar"></i>', Url::to(['/shift-schedule/user', 'id' => $data['ugs_user_id']]), [
                    'title' => 'User Shift Calendar',
                    'target' => '_blank'
                ]),
                Html::a('<i class="fa fa fa-user-plus"></i>', Url::to(['/shift/user-shift-assign/index', 'UserShiftAssignListSearch[userId]' => $data['ugs_user_id']]), [
                    'title' => 'User Shift Assign',
                    'target' => '_blank'
                ])
            ],
            'description' => ''
        ];
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

    public static function getDataForTaskList(array $event, ?string $timezone = "UTC"): array
    {
        $mainTabTitle = '';
        $subTabTitle = '';
        $title = '';

        if (ArrayHelper::keyExists('uss_start_utc_dt', $event)) {
            $startTime = (new \DateTimeImmutable($event['uss_start_utc_dt']))->setTimezone(new \DateTimeZone($timezone));
            $mainTabTitle .= $startTime->format('d M');
            $subTabTitle .= '(' . $startTime->format('H:i');
            $title = $startTime->format('d-M-Y [H:i]');
        }

        if (ArrayHelper::keyExists('uss_end_utc_dt', $event) && !empty($event['uss_end_utc_dt'])) {
            $endTime = (new \DateTimeImmutable($event['uss_end_utc_dt']))->setTimezone(new \DateTimeZone($timezone));
            if ($mainTabTitle != $endTime->format('d M')) {
                $mainTabTitle .= ' - ' . $endTime->format('d M');
            }

            $subTabTitle .= ' - ' . $endTime->format('H:i') . ')';
        } else {
            $subTabTitle .= ')';
        }

        return ['mainTabTitle' => $mainTabTitle, 'subTabTitle' => $subTabTitle, 'title' => $title];
    }
}
