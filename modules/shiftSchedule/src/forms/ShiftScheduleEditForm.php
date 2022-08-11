<?php

namespace modules\shiftSchedule\src\forms;

use frontend\helpers\TimeConverterHelper;
use kartik\daterange\DateRangeBehavior;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use yii\base\Model;

class ShiftScheduleEditForm extends Model
{
    const SCENARIO_EDIT_DRAG_N_DROP = 'dragNDrop';

    public $eventId;
    public $scheduleType;
    public $description;
    public $status;
    public $dateTimeRange;
    public $dateTimeStart;
    public $dateTimeEnd;
    public $duration;
    public $newUserId;
    public $oldUserId;

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
            [['scheduleType', 'status', 'dateTimeRange'], 'required', 'on' => self::SCENARIO_DEFAULT],
            ['scheduleType', 'in', 'range' => array_keys(UserShiftScheduleHelper::getAvailableScheduleTypeList())],
            ['status', 'in', 'range' => array_keys(UserShiftScheduleHelper::getAvailableStatusList())],
            [['description'], 'string', 'max' => 500],
            [['dateTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['dateTimeStart', 'dateTimeEnd', 'defaultDuration'], 'safe'],
            [['dateTimeStart', 'dateTimeEnd'], 'required', 'on' => self::SCENARIO_EDIT_DRAG_N_DROP],
            [['dateTimeStart', 'dateTimeEnd'], 'datetime', 'format' => 'php:Y-m-d H:i'],
            [['newUserId', 'oldUserId'], 'integer'],
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

    public function isChangedUser(): bool
    {
        return $this->oldUserId !== $this->newUserId;
    }

    public function isDragNDropScenario(): bool
    {
        return $this->scenario === self::SCENARIO_EDIT_DRAG_N_DROP;
    }

    public function formName(): string
    {
        return '';
    }
}
