<?php

namespace modules\shiftSchedule\src\forms;

use frontend\helpers\TimeConverterHelper;
use kartik\daterange\DateRangeBehavior;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\base\Model;

class ShiftScheduleEditForm extends Model
{
    public $eventId;
    public $scheduleType;
    public $description;
    public $status;
    public $dateTimeRange;
    public $dateTimeStart;
    public $dateTimeEnd;
    public $duration;

    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'attribute' => 'dateTimeRange',
                'dateStartAttribute' => 'dateTimeStart',
                'dateEndAttribute' => 'dateTimeEnd',
                'dateStartFormat' => 'Y-m-d H:i',
                'dateEndFormat' => 'Y-m-d H:i'
            ]
        ];
    }

    public function rules(): array
    {
        return [
            [['eventId'], 'required'],
            [['scheduleType', 'eventId', 'status'], 'integer', 'skipOnEmpty' => false],
            [['scheduleType'], 'in', 'range' => array_keys(ShiftScheduleType::getList(true))],
            [['status'], 'in', 'range' => array_keys(UserShiftSchedule::getStatusList())],
            [['description'], 'string', 'max' => 500],
            [['dateTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['dateTimeStart', 'dateTimeEnd', 'defaultDuration'], 'safe'],
            [['dateTimeStart', 'dateTimeEnd'], 'datetime', 'format' => 'php:Y-m-d H:i'],
        ];
    }

    public function fillInByEvent(UserShiftSchedule $event, ?string $userTimeZone): void
    {
        $this->eventId = $event->uss_id;
        $this->scheduleType = $event->uss_sst_id;
        $this->status = $event->uss_status_id;
        $this->description = $event->uss_description;

        $timeZone = $userTimeZone ?: 'UTC';
        $startDateTime = (new \DateTimeImmutable($event->uss_start_utc_dt))->setTimezone(new \DateTimeZone($timeZone));
        $endDateTime = (new \DateTimeImmutable($event->uss_end_utc_dt))->setTimezone(new \DateTimeZone($timeZone));

        $this->dateTimeStart = $startDateTime;
        $this->dateTimeEnd = $endDateTime;
        $this->dateTimeRange = $startDateTime->format('Y-m-d H:i') . ' - ' . $endDateTime->format('Y-m-d H:i');
        $this->duration = TimeConverterHelper::minutesToHours($event->uss_duration);
    }
}
