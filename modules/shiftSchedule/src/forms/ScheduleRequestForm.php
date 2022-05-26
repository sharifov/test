<?php

namespace modules\shiftSchedule\src\forms;

use common\models\Employee;
use common\models\Notifications;
use DateTime;
use Exception;
use frontend\widgets\notification\NotificationMessage;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use src\auth\Auth;
use src\helpers\DateHelper;
use Yii;
use yii\base\Model;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Request;

/**
 *
 * @property-write Request $attributesRequest
 * @property-read string $endDateTime
 * @property-read string $startDateTime
 * @property-read string $endDt
 */
class ScheduleRequestForm extends Model
{
    const DESCRIPTION_MAX_LENGTH = 1000;

    const SCENARIO_REQUEST = 'scenario-request';
    const SCENARIO_DECISION = 'scenario-decision';

    public const NOTIFICATION_TO_AGENT = 'notification_to_agent';
    public const NOTIFICATION_TO_SUPERVISER = 'notification_to_superviser';
    public const NOTIFICATION_TYPE_CREATE = 'notification_create';
    public const NOTIFICATION_TYPE_UPDATE = 'notification_update';

    /**
     * @var string
     */
    public string $scheduleType = '';
    /**
     * @var string
     */
    public string $description = '';
    /**
     * @var int
     */
    public int $status = 0;
    /**
     * @var string|null
     */
    public ?string $requestedRangeTime = '';
    /**
     * @var string
     */
    public string $dateTimeStart = '';
    /**
     * @var string
     */
    public string $dateTimeEnd = '';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                'scheduleType',
                'required',
            ],
            [
                'status',
                'required',
            ],
            [
                'description',
                'required',
            ],
            [
                'scheduleType',
                'string',
                'max' => 100,
            ],
            [
                'description',
                'string',
                'max' => self::DESCRIPTION_MAX_LENGTH,
            ],
            [
                'requestedRangeTime',
                'required',
            ],
            [
                'requestedRangeTime',
                'convertDateTimeRange'
            ],
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        return array_merge(
            parent::scenarios(),
            [
                self::SCENARIO_REQUEST => [
                    'requestedRangeTime',
                    'scheduleType',
                    'description',
                ],
                self::SCENARIO_DECISION => [
                    'status',
                    'description',
                ]
            ]
        );
    }

    public function convertDateTimeRange($attribute)
    {
        if ($this->requestedRangeTime) {
            $date = explode(' - ', $this->requestedRangeTime);
            if (count($date) === 2) {
                if (DateHelper::checkDateTime($date[0], 'Y-m-d H:i')) {
                    $this->dateTimeStart = Employee::convertTimeFromUserDtToUTC(strtotime($date[0]));
                } else {
                    $this->addError($attribute, 'Date time start incorrect format');
                    $this->requestedRangeTime = null;
                }
                if (DateHelper::checkDateTime($date[1], 'Y-m-d H:i')) {
                    $this->dateTimeEnd = Employee::convertTimeFromUserDtToUTC(strtotime($date[1]));
                } else {
                    $this->addError($attribute, 'Date time end incorrect format');
                    $this->requestedRangeTime = null;
                }
                if (strtotime($date[0]) >= strtotime($date[1])) {
                    $this->addError($attribute, 'Date time end must be more than Date time start');
                    $this->requestedRangeTime = null;
                }
            } else {
                $this->addError($attribute, 'Requested Range Time is not parsed correctly');
                $this->requestedRangeTime = null;
            }
        }
    }

    /**
     * Save request to user shift schedule
     * @return bool
     * @throws Exception
     */
    public function saveRequest(): bool
    {
        $userShiftSchedule = new UserShiftSchedule($this->mappingData());
        if ($userShiftSchedule->validate() && $userShiftSchedule->save()) {
            $requestModel = new ShiftScheduleRequest([
                'ssr_uss_id' => $userShiftSchedule->uss_id,
                'ssr_sst_id' => $this->scheduleType,
                'ssr_status_id' => ShiftScheduleRequest::STATUS_PENDING,
                'ssr_description' => $this->description,
                'ssr_created_user_id' => Auth::id(),
            ]);
            if ($requestModel->save()) {
                $this->sendNotification(self::NOTIFICATION_TO_SUPERVISER, $requestModel, self::NOTIFICATION_TYPE_CREATE);
                return true;
            }
        }
        return false;
    }

    /**
     * Save Decision request to Shift Schedule Request table
     * @param ShiftScheduleRequest $model
     * @return bool
     */
    public function saveDecision(ShiftScheduleRequest $model): bool
    {
        $scheduleRequest = new ShiftScheduleRequest();
        $scheduleRequest->attributes = $model->attributes;
        $scheduleRequest->ssr_status_id = $this->status;
        $scheduleRequest->ssr_description = $this->description;
        $scheduleRequest->ssr_updated_user_id = Auth::id();
        if ($scheduleRequest->getIsCanEditPreviousDate()) {
            if ($scheduleRequest->save()) {
                $this->sendNotification(self::NOTIFICATION_TO_AGENT, $scheduleRequest);
                $this->sendNotification(self::NOTIFICATION_TO_SUPERVISER, $scheduleRequest, self::NOTIFICATION_TYPE_UPDATE);
                return true;
            }
        }

        return false;
    }

    /**
     * Mapping form data with UserShiftSchedule props, before save
     * @return array
     * @throws Exception
     */
    private function mappingData(): array
    {
        $startDateTime = new \DateTimeImmutable($this->dateTimeStart);
        $endDateTime = new \DateTimeImmutable($this->dateTimeEnd);
        $interval = $startDateTime->diff($endDateTime);
        $diffMinutes = $interval->days * 24 * 60 + $interval->i + ($interval->h * 60);

        return [
            'uss_user_id' => Auth::user()->id,
            'uss_sst_id' => $this->scheduleType,
            'uss_status_id' => UserShiftSchedule::STATUS_PENDING,
            'uss_type_id' => UserShiftSchedule::TYPE_MANUAL,
            'uss_start_utc_dt' => $this->dateTimeStart,
            'uss_end_utc_dt' => $this->dateTimeEnd,
            'uss_duration' => $diffMinutes,
            'uss_description' => $this->description
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'requestedRangeTime' => 'Date Range From / To',
            'scheduleType' => 'Schedule Type',
            'description' => 'Description',
        ];
    }

    /**
     * Setting attribute from request data
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function setAttributesRequest(Request $request): void
    {
        if (!empty($start = $request->get('start')) && !empty($end = $request->get('end'))) {
            if (DateHelper::checkDateTime($start, 'Y-m-d') && DateHelper::checkDateTime($end, 'Y-m-d H:i')) {
                $this->requestedRangeTime = sprintf(
                    '%s - %s',
                    (new DateTime($start))->format('Y-m-d H:i'),
                    (new DateTime($end))->format('Y-m-d H:i')
                );
            }
        }
    }

    /**
     * Send Notification
     * @param string $whom
     * @param ShiftScheduleRequest $scheduleRequest
     * @param string|null $notificationType
     * @return void
     */
    public function sendNotification(string $whom, ShiftScheduleRequest $scheduleRequest, ?string $notificationType = null): void
    {
        $subject = 'Request Status';
        $authUser = Auth::user();
        $startTime = date('Y-m-d H:i:s', strtotime($scheduleRequest->srhUss->uss_start_utc_dt ?? ''));
        $endTime = date('Y-m-d H:i:s', strtotime($scheduleRequest->srhUss->uss_end_utc_dt ?? ''));
        if ($whom === self::NOTIFICATION_TO_AGENT) {
            $body = sprintf(
                'Your %s request for %s - %s was %s by %s',
                $scheduleRequest->getScheduleTypeTitle(),
                $startTime,
                $endTime,
                $scheduleRequest->getStatusName(),
                $authUser->username
            );
            $publishUserIds = [$scheduleRequest->ssr_created_user_id];
        } elseif ($whom === self::NOTIFICATION_TO_SUPERVISER) {
            if ($notificationType === self::NOTIFICATION_TYPE_CREATE) {
                $content = '%s request for %s - %s was created by %s';
            } elseif ($notificationType === self::NOTIFICATION_TYPE_UPDATE) {
                $content = '%s request for %s - %s was updated by %s';
            } else {
                $content = '%s request for %s - %s by %s';
            }
            $body = sprintf(
                $content,
                $scheduleRequest->getScheduleTypeTitle(),
                $startTime,
                $endTime,
                $authUser->username
            );
            $publishUserIds = $authUser->getSupervisionIdsByCurrentUser();
        }

        if (!empty($body) && !empty($publishUserIds)) {
            foreach ($publishUserIds as $userId) {
                if ($ntf = Notifications::create($userId, $subject, $body, Notifications::TYPE_INFO)) {
                    $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $userId], $dataNotification);
                }
            }
        }
    }
}
