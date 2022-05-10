<?php

namespace modules\shiftSchedule\src\forms;

use common\models\Employee;
use kartik\daterange\DateRangeBehavior;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\base\Model;

class SingleEventCreateForm extends Model
{
    public $userId;
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
            [['userId', 'status', 'scheduleType', 'dateTimeRange'], 'required'],
            [['scheduleType', 'userId'], 'integer'],
            [['scheduleType'], 'in', 'range' => array_keys(ShiftScheduleType::getList(true))],
            [['status'], 'in', 'range' => array_keys(UserShiftSchedule::getStatusList())],
            [['description'], 'string', 'max' => 500],
            [['dateTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['dateTimeStart', 'dateTimeEnd', 'defaultDuration'], 'safe'],
            [['dateTimeStart', 'dateTimeEnd'], 'datetime', 'format' => 'php:Y-m-d H:i'],
            [['userId'], 'exist', 'skipOnError' => true, 'skipOnEmpty' => false, 'targetClass' => Employee::class, 'targetAttribute' => 'id']
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
