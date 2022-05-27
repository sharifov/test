<?php

namespace modules\shiftSchedule\src\services;

use common\models\Employee;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\forms\ScheduleDecisionForm;
use modules\shiftSchedule\src\forms\ScheduleRequestForm;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use src\auth\Auth;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class ShiftScheduleRequestService
{
    public const NOTIFICATION_TYPE_CREATE = 'notification_create';
    public const NOTIFICATION_TYPE_UPDATE = 'notification_update';

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
                    'color' => $item->getStatusNameColor(true),
                ];

                $data[] = $dataItem;
            }
        }
        return $data;
    }

    /**
     * Save request to user shift schedule and shift schedule request
     * @param ScheduleRequestForm $requestForm
     * @param Employee $user
     * @return bool
     * @throws \Exception
     */
    public static function saveRequest(ScheduleRequestForm $requestForm, Employee $user): bool
    {
        $startDateTime = new \DateTimeImmutable($requestForm->dateTimeStart);
        $endDateTime = new \DateTimeImmutable($requestForm->dateTimeEnd);
        $interval = $startDateTime->diff($endDateTime);
        $diffMinutes = $interval->days * 24 * 60 + $interval->i + ($interval->h * 60);
        $userShiftSchedule = new UserShiftSchedule([
            'uss_user_id' => $user->id,
            'uss_sst_id' => $requestForm->scheduleType,
            'uss_status_id' => UserShiftSchedule::STATUS_PENDING,
            'uss_type_id' => UserShiftSchedule::TYPE_MANUAL,
            'uss_start_utc_dt' => $requestForm->dateTimeStart,
            'uss_end_utc_dt' => $requestForm->dateTimeEnd,
            'uss_duration' => $diffMinutes,
            'uss_description' => $requestForm->description
        ]);

        if ($userShiftSchedule->validate() && $userShiftSchedule->save()) {
            $requestModel = new ShiftScheduleRequest([
                'ssr_uss_id' => $userShiftSchedule->uss_id,
                'ssr_sst_id' => $requestForm->scheduleType,
                'ssr_status_id' => ShiftScheduleRequest::STATUS_PENDING,
                'ssr_description' => $requestForm->description,
                'ssr_created_user_id' => $user->id,
            ]);
            if ($requestModel->save()) {
                self::sendNotification(
                    Employee::ROLE_SUPERVISION,
                    $requestModel,
                    self::NOTIFICATION_TYPE_CREATE,
                    $user
                );
                return true;
            }
        }
        return false;
    }

    /**
     * Save Decision request to Shift Schedule Request table
     * @param ShiftScheduleRequest $requestModel
     * @param ScheduleDecisionForm $decisionForm
     * @param Employee $user
     * @return bool
     */
    public static function saveDecision(ShiftScheduleRequest $requestModel, ScheduleDecisionForm $decisionForm, Employee $user): bool
    {
        $scheduleRequest = new ShiftScheduleRequest();
        $scheduleRequest->attributes = $requestModel->attributes;
        $scheduleRequest->ssr_status_id = $decisionForm->status;
        $scheduleRequest->ssr_description = $decisionForm->description;
        $scheduleRequest->ssr_updated_user_id = $user->id;
        if ($scheduleRequest->getIsCanEditPreviousDate()) {
            if ($scheduleRequest->save()) {
                if ($requestModel->oldAttributes['ssr_status_id'] !== $decisionForm->status) {
                    self::sendNotification(
                        Employee::ROLE_AGENT,
                        $scheduleRequest,
                        self::NOTIFICATION_TYPE_CREATE,
                        $user
                    );
                    self::sendNotification(
                        Employee::ROLE_SUPERVISION,
                        $scheduleRequest,
                        self::NOTIFICATION_TYPE_UPDATE,
                        $user
                    );
                }
                return true;
            }
        }

        return false;
    }

    /**
     * Send Notification
     * @param string $whom
     * @param ShiftScheduleRequest $scheduleRequest
     * @param string|null $notificationType
     * @param Employee $user
     * @return void
     */
    public static function sendNotification(string $whom, ShiftScheduleRequest $scheduleRequest, ?string $notificationType = null, Employee $user): void
    {
        $subject = 'Request Status';
        if ($whom === Employee::ROLE_AGENT) {
            $publishUserIds = [Employee::findIdentity($scheduleRequest->ssr_created_user_id)];
        } elseif ($whom === Employee::ROLE_SUPERVISION) {
            if ($notificationType === self::NOTIFICATION_TYPE_UPDATE) {
                $publishUserIds = [Employee::findIdentity($user->id)];
            } else {
                $publishUserIds = UserShiftScheduleHelper::getSupervisionByUsers($user->id);
            }
        }

        if (!empty($publishUserIds)) {
            foreach ($publishUserIds as $userModel) {
                $timezone = $userModel->timezone ?? null;
                $startTime = Yii::$app->formatter->asDateTimeByUserTimezone(
                    strtotime($scheduleRequest->srhUss->uss_start_utc_dt ?? ''),
                    $timezone
                );
                $endTime = Yii::$app->formatter->asDateTimeByUserTimezone(
                    strtotime($scheduleRequest->srhUss->uss_end_utc_dt ?? ''),
                    $timezone
                );

                $dateTime = new \DateTime($scheduleRequest->srhUss->uss_start_utc_dt ?? '', new \DateTimeZone($timezone));
                $body = sprintf(
                    "%s %s request (Id: %s) for %s - %s (%s %s) was %s by %s \n<br>Description: %s",
                    ($whom === Employee::ROLE_AGENT ? 'Your' : ($scheduleRequest->ssrCreatedUser->username ?? '')),
                    $scheduleRequest->getScheduleTypeTitle(),
                    $scheduleRequest->ssr_uss_id,
                    $startTime,
                    $endTime,
                    $timezone,
                    $dateTime->format('P'),
                    $scheduleRequest->getStatusNamePasteTense(),
                    $user->username,
                    $scheduleRequest->ssr_description
                );

                if ($ntf = Notifications::create($userModel->id, $subject, $body, Notifications::TYPE_INFO)) {
                    $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $userModel->id], $dataNotification);
                }
            }
        }
    }
}
