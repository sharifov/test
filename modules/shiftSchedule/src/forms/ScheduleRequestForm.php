<?php

namespace modules\shiftSchedule\src\forms;

use DateInterval;
use DateTime;
use Exception;
use src\helpers\app\AppHelper;
use Yii;
use yii\base\Model;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\web\Request;

/**
 *
 * @property-write Request $attributesRequest
 * @property-read string $endDt
 */
class ScheduleRequestForm extends Model
{
    const DATE_FORMAT = 'Y-m-d';
    const MIN_DAYS_DURATION = 0;
    const MAX_DAYS_DURATION = 20;
    const SCENARIO_DATETIME = 'scenario-datetime';

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
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                [
                    'startDt',
                    'scheduleType',
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
        ];
    }

    /**
     * Save request to user shift schedule
     * @return bool
     * @throws Exception
     */
    public function saveRequest(): bool
    {
        $userShiftSchedule = new UserShiftSchedule($this->mappingData());
        return $userShiftSchedule->validate() && $userShiftSchedule->save();
    }

    /**
     * Mapping form data with UserShiftSchedule props, before save
     * @return array
     * @throws Exception
     */
    private function mappingData(): array
    {
        return [
            'uss_user_id' => Yii::$app->user->getId(),
            'uss_sst_id' => $this->scheduleType,
            'uss_status_id' => UserShiftSchedule::STATUS_PENDING,
            'uss_type_id' => UserShiftSchedule::TYPE_MANUAL,
            'uss_start_utc_dt' => $this->startDt,
            'uss_end_utc_dt' => $this->getEndDt(),
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
            $start->add(new DateInterval('P' . $this->duration . 'D'));
            return $start->format(self::DATE_FORMAT);
        }
        return '';
    }
}
