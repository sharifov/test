<?php

namespace modules\shiftSchedule\src\services;

use common\components\jobs\ShiftScheduleRequestNotificationsAfterSaveJob;
use common\models\Employee;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use modules\featureFlag\FFlag;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\repository\ShiftScheduleRequestRepository;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\events\ShiftScheduleSaveRequestEvent;
use modules\shiftSchedule\src\forms\ScheduleDecisionForm;
use modules\shiftSchedule\src\forms\ScheduleRequestForm;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

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
     * @param string $endDate
     * @return array
     */
    public static function getTimelineListByUserList(ActiveQuery $userList, string $startDate, string $endDate): array
    {
        $query = ShiftScheduleRequestSearch::getSearchQuery($userList, null, $startDate, $endDate);
        return $query->all();
    }

    /**
     * @param ShiftScheduleRequest[] $timelineList
     * @param string $userTimeZone
     * @return array
     */
    public static function getCalendarTimelineJsonData(array $timelineList, string $userTimeZone): array
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
                    'start' => Yii::$app->formatter->asDateTimeByUserTimezone(
                        strtotime($item->srhUss->uss_start_utc_dt ?? ''),
                        $userTimeZone,
                        'php: c'
                    ),
                    'end' => Yii::$app->formatter->asDateTimeByUserTimezone(
                        strtotime($item->srhUss->uss_end_utc_dt ?? ''),
                        $userTimeZone,
                        'php: c'
                    ),
                    'resource' => 'us-' . $item->ssr_created_user_id,
                    'extendedProps' => [
                        'icon' => $item->srhSst->sst_icon_class,
                        'ussId' => $item->ssr_uss_id,
//                        'backgroundImage' => 'linear-gradient(45deg, ' . $sstColor . ' 30%, ' . $statusColor . ' 70%)',
                    ],
                    'color' => $item->getStatusNameColor(true),
                    'display' => 'block',
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

                /** @fflag FFlag::FF_KEY_SHIFT_SCHEDULE_REQUEST_SAVE_SEND_NOTIFICATION_BY_JOB_ENABLE, Enable send notification from job, when request was saved */
                if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_SHIFT_SCHEDULE_REQUEST_SAVE_SEND_NOTIFICATION_BY_JOB_ENABLE)) {
                    $job = new ShiftScheduleRequestNotificationsAfterSaveJob();
                    $job->shiftScheduleRequest = $requestModel;
                    $job->employee = $user;

                    Yii::$app->queue_job->push($job);
                } else {
                    self::sendNotification(
                        Employee::ROLE_SUPERVISION,
                        $requestModel,
                        $user,
                        self::NOTIFICATION_TYPE_CREATE
                    );
                }

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
    public static function saveDecision(ShiftScheduleRequest $requestModel, ScheduleDecisionForm $decisionForm, ?Employee $user = null): bool
    {
        $oldStatus = $requestModel->ssr_status_id;
        $requestModel->ssr_status_id = $decisionForm->status;
        $requestModel->ssr_description = $decisionForm->description;
        $requestModel->ssr_updated_user_id = $user->id ?? null;
        if ($requestModel->save()) {
            if ($requestModel->isChangedStatus($oldStatus)) {
                if ($user !== null) {
                    self::sendNotification(
                        Employee::ROLE_AGENT,
                        $requestModel,
                        $user,
                        self::NOTIFICATION_TYPE_CREATE
                    );
                    self::sendNotification(
                        Employee::ROLE_SUPERVISION,
                        $requestModel,
                        $user,
                        self::NOTIFICATION_TYPE_UPDATE
                    );
                }

                Notifications::pub(
                    ['user-' . $requestModel->ssr_created_user_id],
                    'reloadShitScheduleRequest',
                    [
                        'data' => [],
                    ],
                );
            }
            return true;
        }

        return false;
    }

    /**
     * @param UserShiftSchedule $event
     * @param UserShiftSchedule $oldEvent
     * @param Employee $user
     * @return void
     */
    public function changeDueToEventChange(UserShiftSchedule $event, UserShiftSchedule $oldEvent, array $changedAttributes, Employee $user)
    {
        $neededAttributes = self::getNeededAttributesWithMessage($oldEvent, $changedAttributes);

        /** @var ShiftScheduleRequest $requestModel */
        $requestModel = ShiftScheduleRequest::find()
            ->andWhere(['ssr_uss_id' => $event->uss_id])
            ->andWhere(['ssr_status_id' => ShiftScheduleRequest::STATUS_PENDING])
            ->one();

        if (!$requestModel || count($neededAttributes) == 0) {
            return;
        }

        $requestModel->ssr_status_id = ShiftScheduleRequest::STATUS_DECLINED;
        $requestModel->ssr_description = 'Shift event was updated (' . implode(',', $neededAttributes) . ')';
        $requestModel->ssr_updated_user_id = $user->id;

        (new ShiftScheduleRequestRepository($requestModel))->save(true);

        self::sendNotification(
            Employee::ROLE_AGENT,
            $requestModel,
            $user,
            self::NOTIFICATION_TYPE_CREATE
        );

        self::sendNotification(
            Employee::ROLE_SUPERVISION,
            $requestModel,
            $user,
            self::NOTIFICATION_TYPE_UPDATE
        );

        Notifications::pub(
            ['user-' . $requestModel->ssr_created_user_id],
            'reloadShitScheduleRequest',
            [
                'data' => [],
            ],
        );
    }

    /**
     * @param string $whom
     * @param ShiftScheduleRequest $scheduleRequest
     * @param Employee $user
     * @param string|null $notificationType
     * @return void
     * @throws \Exception
     */
    public static function sendNotification(string $whom, ShiftScheduleRequest $scheduleRequest, Employee $user, ?string $notificationType = null): void
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
                $timezone = $userModel->timezone ?: 'UTC';
                $startTime = Yii::$app->formatter->asDateTimeByUserTimezone(
                    strtotime($scheduleRequest->srhUss->uss_start_utc_dt ?? ''),
                    $timezone
                );
                $endTime = Yii::$app->formatter->asDateTimeByUserTimezone(
                    strtotime($scheduleRequest->srhUss->uss_end_utc_dt ?? ''),
                    $timezone
                );

                $dateTime = new \DateTime($scheduleRequest->srhUss->uss_start_utc_dt ?? '', new \DateTimeZone($timezone));
                if ($whom === Employee::ROLE_SUPERVISION && $notificationType === self::NOTIFICATION_TYPE_CREATE) {
                    $body = sprintf(
                        "%s sent %s request (Id: %s) for %s - %s (%s %s) \n<br>Description: %s",
                        $scheduleRequest->ssrCreatedUser->username ?? '',
                        $scheduleRequest->getScheduleTypeTitle(),
                        $scheduleRequest->ssr_uss_id,
                        $startTime,
                        $endTime,
                        $timezone,
                        $dateTime->format('P'),
                        $scheduleRequest->ssr_description
                    );
                } else {
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
                }

                if ($ntf = Notifications::create($userModel->id, $subject, $body, Notifications::TYPE_INFO)) {
                    $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $userModel->id], $dataNotification);
                }
            }
        }
    }


    private static function getNeededAttributesWithMessage(UserShiftSchedule $oldEvent, array $changedAttributes): array
    {
        $newAttributes = [];

        foreach ($changedAttributes as $key => $value) {
            switch ($key) {
                case 'uss_status_id':
                    $newAttributes[$key] =
                        Yii::t('app', 'Status from {oldAttr} to {newAttr}', [
                            'oldAttr' => UserShiftSchedule::getStatusList()[$oldEvent->uss_status_id],
                            'newAttr' => UserShiftSchedule::getStatusList()[$value]
                        ]);
                    break;
                case 'uss_sst_id':
                    $newAttributes[$key] =
                        Yii::t('app', 'ShiftScheduleType from {oldAttr} to {newAttr}', [
                            'oldAttr' => $oldEvent->shiftScheduleType->sst_name ?? '',
                            'newAttr' => ($shiftScheduleType = ShiftScheduleType::findOne($value)) ? $shiftScheduleType->sst_name : ''
                        ]);
                    break;

                case 'uss_start_utc_dt':
                    $newAttributes[$key] = Yii::t('app', 'Start DateTime (UTC) from {oldAttr} to {newAttr}', [
                        'oldAttr' => $oldEvent->{$key},
                        'newAttr' => $value
                    ]);
                    break;

                case 'uss_end_utc_dt':
                    $newAttributes[$key] = Yii::t('app', 'End DateTime (UTC) from {oldAttr} to {newAttr}', [
                        'oldAttr' => $oldEvent->{$key},
                        'newAttr' => $value
                    ]);
                    break;
            }
        }
        return $newAttributes;
    }
}
