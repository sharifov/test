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
                'on' => self::SCENARIO_DECISION,
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
            $requestModel->save();
            return true;
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
                $subject = 'Request Status';
                $body = 'Your ' . Html::a('request', Url::to(['shift-schedule/index'])) . ' status was change to “' .
                    $scheduleRequest::getList()[$scheduleRequest->ssr_status_id] . '” ' . "<br>" .
                    (!empty($scheduleRequest->ssr_description) ? "Description: “" . $scheduleRequest->ssr_description . '”' : '');
                if ($ntf = Notifications::create($scheduleRequest->ssr_created_user_id, $subject, $body, Notifications::TYPE_INFO)) {
                    $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $scheduleRequest->ssr_created_user_id], $dataNotification);
                }
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
}
