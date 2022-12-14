<?php

namespace modules\shiftSchedule\src\forms;

use common\components\validators\CheckJsonValidator;
use common\components\validators\IsArrayValidator;
use kartik\daterange\DateRangeBehavior;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use yii\base\Model;
use yii\helpers\Json;

class UserShiftCalendarMultipleUpdateForm extends Model
{
    public $eventIds;
    public $showForm;
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
            ['scheduleType', 'in', 'range' => array_keys(UserShiftScheduleHelper::getAvailableScheduleTypeList())],
            ['status', 'in', 'range' => array_keys(UserShiftScheduleHelper::getAvailableStatusList())],
            [['description'], 'string', 'max' => 500],
            [['dateTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['defaultDuration'], 'match', 'pattern' => '/^(\d+):[0-5][0-9]$/'],
            [['dateTimeStart', 'dateTimeEnd'], 'datetime', 'format' => 'php:Y-m-d H:i'],
            [['showForm'], 'boolean'],
            [['showForm'], 'default', 'value' => false]
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
