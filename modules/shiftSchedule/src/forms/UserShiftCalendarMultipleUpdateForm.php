<?php

namespace modules\shiftSchedule\src\forms;

use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use kartik\daterange\DateRangeBehavior;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\base\Model;

class UserShiftCalendarMultipleUpdateForm extends Model
{
    public $eventIds;
    public $scheduleType;
    public $description;
    public $status;
    public $dateTimeRange;
    public $dateTimeStart;
    public $dateTimeEnd;
    public $defaultDuration;

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
            [['eventIds'], 'required'],
            [['eventIds'], CheckJsonValidator::class],
            [['scheduleType'], 'integer'],
            [['scheduleType'], 'in', 'range' => array_keys(ShiftScheduleType::getList(true))],
            [['status'], 'in', 'range' => array_keys(UserShiftSchedule::getStatusList())],
            [['description'], 'string', 'max' => 500],
            [['dateTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['defaultDuration'], 'match', 'pattern' => '/^(\d+):[0-5][0-9]$/'],
            [['dateTimeStart', 'dateTimeEnd'], 'datetime', 'format' => 'php:Y-m-d H:i'],
        ];
    }
}
