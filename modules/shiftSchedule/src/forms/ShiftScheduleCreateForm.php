<?php

namespace modules\shiftSchedule\src\forms;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\base\Model;

class ShiftScheduleCreateForm extends Model
{
    public $userGroups;
    public $users;
    public $scheduleType;
    public $description;
    public $startDateTime;
    public $duration;
    public $getUsersByGroups;
    public $status;

    private array $_usersBatch = [];

    public function rules()
    {
        return [
            [['users', 'status', 'scheduleType', 'startDateTime', 'duration'], 'required'],
            [['userGroups'], IsArrayValidator::class],
            [['users'], 'string'],
            [['users'], 'validateUsers', 'skipOnEmpty' => true],
            [['userGroups'], 'each', 'rule' => ['filter', 'filter' => 'intval']],
            [['scheduleType'], 'integer'],
            [['scheduleType'], 'in', 'range' => array_keys(ShiftScheduleType::getList(true))],
            [['status'], 'in', 'range' => array_keys(UserShiftSchedule::getStatusList())],
            [['description'], 'string', 'max' => 500],
            [['startDateTime'], 'datetime', 'format' => 'php:Y-m-d H:i'],
            [['duration'], 'date', 'format' => 'php:H:i'],
            [['getUsersByGroups'], 'boolean'],
            [['getUsersByGroups'], 'default', 'value' => false],
        ];
    }

    public function load($data, $formName = null): bool
    {
        $parentLoad = parent::load($data, $formName);
        $users = explode(',', $this->users);
        if (!empty($users)) {
            $this->_usersBatch = $users;
        }
        return $parentLoad;
    }

    public function validateUsers(): bool
    {
        if (empty($this->_usersBatch)) {
            $this->addError('users', 'Users are not selected');
            return false;
        }
        return true;
    }

    public function getUsersBatch(): array
    {
        return $this->_usersBatch;
    }
}
