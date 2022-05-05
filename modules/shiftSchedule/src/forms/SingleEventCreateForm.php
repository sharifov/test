<?php

namespace modules\shiftSchedule\src\forms;

use common\models\Employee;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\base\Model;

class SingleEventCreateForm extends Model
{
    public $userId;
    public $scheduleType;
    public $description;
    public $startDateTime;
    public $duration;
    public $status;

    public function rules(): array
    {
        return [
            [['userId', 'status', 'scheduleType', 'startDateTime', 'duration'], 'required'],
            [['scheduleType', 'userId'], 'integer'],
            [['scheduleType'], 'in', 'range' => array_keys(ShiftScheduleType::getList(true))],
            [['status'], 'in', 'range' => array_keys(UserShiftSchedule::getStatusList())],
            [['description'], 'string', 'max' => 500],
            [['startDateTime'], 'datetime', 'format' => 'php:Y-m-d H:i'],
            [['duration'], 'date', 'format' => 'php:H:i'],
            [['userId'], 'exist', 'skipOnError' => true, 'skipOnEmpty' => false, 'targetClass' => Employee::class, 'targetAttribute' => 'id']
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
