<?php

namespace modules\shiftSchedule\src\forms;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use kartik\daterange\DateRangeBehavior;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use src\auth\Auth;
use src\helpers\DateHelper;
use Yii;
use yii\base\Model;

class ShiftScheduleCreateForm extends Model
{
    public $userGroups;
    public $users;
    public $scheduleType;
    public $description;
    public $getUsersByGroups;
    public $status;
    public $dateTimeRange;
    public $dateTimeStart;
    public $dateTimeEnd;
    public $defaultDuration;

    private array $_usersBatch = [];
    private const ATTRIBUTE_DATE_RANGE = 'dateTimeRange';
    private const SEPARATOR_DATE_RANGE = ' - ';

    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'attribute' => self::ATTRIBUTE_DATE_RANGE,
                'separator' => self::SEPARATOR_DATE_RANGE,
                'dateStartAttribute' => 'dateTimeStart',
                'dateEndAttribute' => 'dateTimeEnd',
                'dateStartFormat' => 'Y-m-d H:i',
                'dateEndFormat' => 'Y-m-d H:i'
            ]
        ];
    }

    public function rules()
    {
        return [
            [['users', 'status', 'scheduleType', 'dateTimeRange'], 'required'],
            [['userGroups'], IsArrayValidator::class],
            [['users'], 'string'],
            [['users'], 'validateUsers', 'skipOnEmpty' => true],
            [['userGroups'], 'each', 'rule' => ['filter', 'filter' => 'intval']],
            [['scheduleType'], 'integer'],
            [['scheduleType'], 'in', 'range' => array_keys(ShiftScheduleType::getList(true))],
            [['status'], 'in', 'range' => array_keys(UserShiftSchedule::getStatusList())],
            [['description'], 'string', 'max' => 500],
            [['dateTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            ['dateTimeRange', 'validateStartTime'],
            [['dateTimeStart', 'dateTimeEnd', 'defaultDuration'], 'safe'],
            [['dateTimeStart', 'dateTimeEnd'], 'datetime', 'format' => 'php:Y-m-d H:i'],
            [['getUsersByGroups'], 'boolean'],
            [['getUsersByGroups'], 'default', 'value' => false],
        ];
    }

    public function validateStartTime($attribute, $params)
    {
        $dates = explode(self::SEPARATOR_DATE_RANGE, $this->{self::ATTRIBUTE_DATE_RANGE}, 2);
        if (count($dates) !== 2) {
            $this->addError($attribute, 'Date Time Range incorrect format');
            return;
        }
        if (!DateHelper::checkDateTime($dates[0], 'Y-m-d H:i')) {
            $this->addError($attribute, 'Start DateTime incorrect format');
            return;
        }

        $startDateTime = new \DateTimeImmutable($dates[0], ($timezone = Auth::user()->timezone) ? new \DateTimeZone($timezone) : null);
        $nowDateTime = new \DateTimeImmutable('now', ($timezone = Auth::user()->timezone) ? new \DateTimeZone($timezone) : null);
        if ($startDateTime < $nowDateTime) {
            $this->addError($attribute, 'Start DateTime must be more than now');
        }
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
