<?php

namespace frontend\models\form\shiftSchedule;

use Yii;
use yii\base\Model;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;

class ScheduleRequestForm extends Model
{
    /**
     * @var string
     */
    public string $startDt = '';
    /**
     * @var string
     */
    public string $endDt = '';
    /**
     * @var string
     */
    public string $scheduleType = '';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                [
                    'startDt',
                    'endDt',
                    'scheduleType',
                ],
                'required',
            ],
            [
                [
                    'startDt',
                    'endDt',
                ],
                'validateDates',
                'skipOnEmpty' => true,
            ],
            [
                'scheduleType',
                'string',
                'max' => 100,
            ],
        ];
    }

    /**
     * Save request to user shift schedule
     * @return bool
     */
    public function saveRequest(): bool
    {
        $userShiftSchedule = new UserShiftSchedule($this->mappingData());
        return $userShiftSchedule->validate() && $userShiftSchedule->save();
    }

    /**
     * Mapping form data with UserShiftSchedule props, before save
     * @return array
     */
    private function mappingData(): array
    {
        return [
            'uss_user_id' => Yii::$app->user->getId(),
            'uss_sst_id' => $this->scheduleType,
            'uss_status_id' => UserShiftSchedule::STATUS_PENDING,
            'uss_type_id' => UserShiftSchedule::TYPE_MANUAL,
            'uss_start_utc_dt' => $this->startDt,
            'uss_end_utc_dt' => $this->endDt,
        ];
    }

    /**
     * Inline validator for dates
     * @return void
     */
    public function validateDates(): void
    {
        if (strtotime($this->startDt) < strtotime(date('Y-m-d'))) {
            $this->addError('startDt', 'Invalid start date');
        }
        if (strtotime($this->endDt) < strtotime(date('Y-m-d'))) {
            $this->addError('endDt', 'Invalid end date');
        }
        if (strtotime($this->endDt) <= strtotime($this->startDt)) {
            $this->addError('startDt', 'Please give correct Start and End dates');
            $this->addError('endDt', 'Please give correct Start and End dates');
        }
    }
}
