<?php

namespace modules\shiftSchedule\src\forms;

use common\models\Employee;
use common\models\Notifications;
use DateInterval;
use DateTime;
use Exception;
use frontend\widgets\notification\NotificationMessage;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use src\auth\Auth;
use src\helpers\app\AppHelper;
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
    const DATE_FORMAT = 'Y-m-d';
    const DATETIME_FORMAT = 'Y-m-d H:i';
    const MIN_DAYS_DURATION = 1;
    const MAX_DAYS_DURATION = 20;
    const DESCRIPTION_MAX_LENGTH = 1000;

    const SCENARIO_DECISION = 'scenario-decision';

    /**
     * @var string
     */
    public string $startDt = '';
    /**
     * @var string
     */
    public string $scheduleType = '';
    /**
     * @var int|null
     */
    public ?int $duration = null;

    /**
     * @var string
     */
    public string $startTime = '';
    /**
     * @var string
     */
    public string $endTime = '';
    /**
     * @var string
     */
    public string $description = '';
    /**
     * @var int
     */
    public int $status = 0;

    /**
     * @return void
     */
    public function init(): void
    {
        if (empty($this->startDt)) {
            $this->startDt = date(self::DATE_FORMAT);
        }
        if (empty($this->startTime)) {
            $this->startTime = '00:00';
        }
        if (empty($this->endTime)) {
            $this->endTime = '23:59';
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                [
                    'startDt',
                    'scheduleType',
                    'startTime',
                    'endTime',
                ],
                'required',
            ],
            [
                [
                    'status',
                ],
                'required',
            ],
            [
                [
                    'startDt',
                ],
                'validateDates',
                'skipOnEmpty' => true,
            ],
            [
                'scheduleType',
                'string',
                'max' => 100,
            ],
            [
                'duration',
                'number',
                'min' => self::MIN_DAYS_DURATION,
                'max' => self::MAX_DAYS_DURATION,
            ],
            [
                [
                    'startTime',
                    'endTime',
                ],
                'string',
                'length' => 5,
            ],
            [
                [
                    'startTime',
                    'endTime',
                ],
                'validateTime',
                'skipOnEmpty' => true,
            ],
            [
                [
                    'startTime',
                    'endTime',
                ],
                'validateChronologicallyTime',
                'skipOnEmpty' => true,
            ],
            [
                'description',
                'string',
                'max' => self::DESCRIPTION_MAX_LENGTH,
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
                self::SCENARIO_DECISION => [
                    'status',
                    'description',
                ]
            ]
        );
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
                'srh_uss_id' => $userShiftSchedule->uss_id,
                'srh_sst_id' => $this->scheduleType,
                'srh_status_id' => ShiftScheduleRequest::STATUS_PENDING,
                'srh_description' => $this->description,
                'srh_start_utc_dt' => $this->getStartDateTime(),
                'srh_end_utc_dt' => $this->getEndDateTime(),
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
        $scheduleRequest->srh_status_id = $this->status;
        $scheduleRequest->srh_description = $this->description;
        if ($scheduleRequest->save()) {
            $subject = 'Request Status';
            $body = 'Your ' . Html::a('request', Url::to(['shift-schedule/index'])) . ' status was change to “' .
                    $scheduleRequest::getList()[$scheduleRequest->srh_status_id] . '” ' . "<br>" .
                (!empty($scheduleRequest->srh_description) ? "Description: “" . $scheduleRequest->srh_description . '”' : '');
            if ($ntf = Notifications::create($scheduleRequest->srh_created_user_id, $subject, $body, Notifications::TYPE_INFO)) {
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $scheduleRequest->srh_created_user_id], $dataNotification);
            }
            return true;
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
        return [
            'uss_user_id' => Auth::user()->id,
            'uss_sst_id' => $this->scheduleType,
            'uss_status_id' => UserShiftSchedule::STATUS_PENDING,
            'uss_type_id' => UserShiftSchedule::TYPE_MANUAL,
            'uss_start_utc_dt' => $this->getStartDateTime(),
            'uss_end_utc_dt' => $this->getEndDateTime(),
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'startDt' => Yii::t('schedule-request', 'Start Date'),
            'duration' => Yii::t('schedule-request', 'Duration'),
            'startTime' => Yii::t('schedule-request', 'Start Time'),
            'endTime' => Yii::t('schedule-request', 'End Time'),
            'scheduleType' => Yii::t('schedule-request', 'Schedule Type'),
            'description' => Yii::t('schedule-request', 'Description'),
        ];
    }

    /**
     * Inline validator for dates
     * @param string $attribute
     * @return void
     */
    public function validateDates(string $attribute): void
    {
        if (strtotime($this->$attribute) < strtotime(date(self::DATE_FORMAT))) {
            $this->addError($attribute, Yii::t('schedule-request', 'Invalid start date'));
        }
    }

    /**
     * Inline validator for times
     * @param string $attribute
     * @return void
     */
    public function validateTime(string $attribute): void
    {
        $currentDate = date(self::DATE_FORMAT);
        if (!AppHelper::validateDate($currentDate . ' ' . $this->$attribute, self::DATETIME_FORMAT)) {
            $this->addError($attribute, Yii::t(
                'schedule-request',
                'Invalid {attribute}',
                [
                    'attribute' => self::getAttributeLabel($attribute),
                ]
            ));
        }
    }

    /**
     * Inline validator for chronologically time
     * @return void
     * @throws Exception
     */
    public function validateChronologicallyTime(): void
    {
        if (AppHelper::validateDate($this->startDt) && !empty($endDate = $this->getEndDt())) {
            $startDate = new DateTime($this->startDt);
            $start = $startDate->format(self::DATE_FORMAT) . ' ' . $this->startTime;
            $end = $endDate . ' ' . $this->endTime;
            if (AppHelper::validateDate($start, self::DATETIME_FORMAT) && AppHelper::validateDate($end, self::DATETIME_FORMAT)) {
                if (new DateTime($start) > new DateTime($end)) {
                    $errorMsg = Yii::t('schedule-request', 'Times are not in the chronologically order');
                    $this->addError('startTime', $errorMsg);
                    $this->addError('endTime', $errorMsg);
                }
            }
        }
    }

    /**
     * Setting attribute from request data
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function setAttributesRequest(Request $request): void
    {
        if (!empty($start = $request->get('start'))) {
            if (AppHelper::validateDate($start, self::DATE_FORMAT)) {
                $start = new DateTime($start);
                $this->startDt = $start->format(self::DATE_FORMAT);
                $this->validate(['startDt']);

                if (!empty($end = $request->get('end'))) {
                    if (AppHelper::validateDate($end, self::DATE_FORMAT)) {
                        $end = new DateTime($end);
                        $this->duration = $end->diff($start)->format("%a");
                    }
                }
            }
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getEndDt(): string
    {
        if (AppHelper::validateDate($this->startDt, self::DATE_FORMAT) && !empty($this->duration)) {
            $start = new DateTime($this->startDt);
            $start->add(new DateInterval('P' . ($this->duration - 1) . 'D'));
            return $start->format(self::DATE_FORMAT);
        }
        return '';
    }

    /**
     * Convert datetime to UTC
     * @param string $dateTime
     * @return string
     */
    public function convertToUTC(string $dateTime): string
    {
        return Employee::convertToUTC(
            strtotime($dateTime),
            Auth::user()->timezone
        );
    }

    /**
     * Return start date with time
     * @return string
     */
    public function getStartDateTime(): string
    {
        return $this->convertToUTC(sprintf(
            '%s %s',
            $this->startDt,
            $this->startTime
        ));
    }

    /**
     * Return end date with time
     * @return string
     * @throws Exception
     */
    public function getEndDateTime(): string
    {
        return $this->convertToUTC(sprintf(
            '%s %s',
            $this->getEndDt(),
            $this->endTime
        ));
    }
}
