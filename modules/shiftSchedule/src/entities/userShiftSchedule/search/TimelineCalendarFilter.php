<?php

namespace modules\shiftSchedule\src\entities\userShiftSchedule\search;

use common\components\validators\IsArrayValidator;
use yii\base\Model;

class TimelineCalendarFilter extends Model
{
    public $startDate = '';
    public $endDate = '';
    public $userGroups = [];
    public $users = [];
    public $statuses = [];
    public $scheduleTypes = [];
    public $duration;
    public $startDateTime;
    public $startDateTimeCondition;
    public $endDateTime;
    public $endDateTimeCondition;
    public $shift = [];
    public $userId;
    public $appliedFilter;

    public array $parsedUserGroups = [];

    public const LESS_THEN_OR_EQUAL = 1;
    public const MORE_THEN_OR_EQUAL = 2;
    public const EQUAL = 3;

    public const CONDITION_LIST = [
        self::LESS_THEN_OR_EQUAL => '<=',
        self::MORE_THEN_OR_EQUAL => '>=',
        self::EQUAL => '='
    ];

    public const CONDITION_NAME_LIST = [
        self::LESS_THEN_OR_EQUAL => 'Less Then Or Equal',
        self::MORE_THEN_OR_EQUAL => 'More Then Or Equal',
        self::EQUAL => 'Equal'
    ];

    public function rules()
    {
        return [
            [['userGroups', 'userId'], 'required'],
            [['userId'], 'integer'],
            [['startDateTimeCondition'], 'required', 'when' => function (): bool {
                return !empty($this->startDateTime);
            }],
            [['endDateTimeCondition'], 'required', 'when' => function (): bool {
                return !empty($this->endDateTime);
            }],
            [['users', 'statuses', 'scheduleTypes', 'shift', 'userGroups'], 'default', 'value' => []],
            [['users', 'statuses', 'scheduleTypes', 'shift', 'userGroups'], IsArrayValidator::class],
            [['startDateTime', 'endDateTime', 'startDateTimeCondition', 'endDateTimeCondition', 'duration', 'startDate', 'endDate'], 'string'],
            [['startDateTime', 'endDateTime'], 'datetime', 'format' => 'php:Y-m-d H:i'],
            [['startDate', 'endDate'], 'datetime', 'format' => 'php:Y-m-d'],
            [['appliedFilter'], 'boolean'],
            [['appliedFilter'], 'default', 'value' => false]
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public static function getConditionNameList(): array
    {
        return self::CONDITION_NAME_LIST;
    }

    public function getStartDateTimeConditionOperator(): string
    {
        return self::CONDITION_LIST[$this->startDateTimeCondition] ?? '=';
    }

    public function getEndDateTimeConditionOperator(): string
    {
        return self::CONDITION_LIST[$this->endDateTimeCondition] ?? '=';
    }
}
