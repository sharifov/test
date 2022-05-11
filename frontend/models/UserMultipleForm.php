<?php

namespace frontend\models;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use src\auth\Auth;
use src\model\clientChatChannel\entity\ClientChatChannel;
use yii\base\Model;

/**
 * UserMultipleForm form
 */
class UserMultipleForm extends Model
{
    public $user_list;
    public $user_list_json;
    public $up_call_expert_limit;
    public $userDepartments;
    public $userRoles;
    public $status;
    public $workStart;
    public $workMinutes;
    public $timeZone;
    public $inboxShowLimitLeads;
    public $defaultTakeLimitLeads;
    public $minPercentForTakeLeads;
    public $frequencyMinutes;
    public $baseAmount;
    public $commissionPercent;
    public $autoRedial;
    public $kpiEnable;
    public $status_id;
    public $leaderBoardEnabled;
    public $userClientChatChanels;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['userRoles', IsArrayValidator::class],
            ['userRoles', 'userRolesValidate', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['userDepartments', IsArrayValidator::class],
            ['userDepartments', 'userDepartmentsValidate', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['userClientChatChanels', IsArrayValidator::class],
            ['userClientChatChanels', 'clientChatChanelsValidate', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['status', 'integer'],
            ['status', 'in', 'range' => array_keys(Employee::STATUS_LIST)],

            [['user_list_json'], 'required'],
            [['baseAmount', 'commissionPercent'], 'number'],
            [['up_call_expert_limit', 'status_id', 'inboxShowLimitLeads', 'defaultTakeLimitLeads', 'minPercentForTakeLeads', 'frequencyMinutes'], 'integer'],
            [['user_list_json', 'workStart', 'workMinutes', 'timeZone', 'autoRedial', 'kpiEnable', 'leaderBoardEnabled'], 'safe'],
            [['user_list_json'], 'filter', 'filter' => function ($value) {
                try {
                    $data = \yii\helpers\Json::decode($value);

                    if (!is_array($data)) {
                        $this->addError('user_list_json', 'Invalid JSON data for decode');
                        return null;
                    }

                    foreach ($data as $userId) {
                        $model = Employee::findOne($userId);
                        if (!$model) {
                            $this->addError('user_list_json', 'Not found Employee ID: ' . $userId);
                            return null;
                        }
                    }

                    $this->user_list = $data;

                    return $value;
                } catch (\yii\base\Exception $e) {
                    $this->addError('user_list_json', $e->getMessage());
                    return null;
                }
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_list'             => 'Selected Users',
            'user_list_json'        => 'Selected Users JSON',
            'up_call_expert_limit'  => 'Call Expert Limit',
            'userRoles' => 'Roles',
            'userDepartments' => 'Departments',
            'workStart' => 'Work Start Time',
            'timeZone' => 'Timezone',
            'frequencyMinutes' => 'Take Frequency Minutes',
            'autoRedial' => 'Auto redial',
            'kpiEnable' => 'KPI enable',
            'leaderBoardEnabled' => 'Leader Board Enabled',
            'userClientChatChanels' => 'Client Chat Chanels'
        ];
    }

    public function getRoles(): array
    {
        return \common\models\Employee::getAllRoles(Auth::user());
    }

    public function getDepartments(): array
    {
        return \common\models\Department::getList();
    }

    public function getClientChatChanels(): array
    {
        return ClientChatChannel::getList();
    }

    public function userRolesValidate(): void
    {
        $roles = array_keys($this->getRoles());
        foreach ($this->userRoles as $role) {
            if (!in_array($role, $roles, true)) {
                $this->addError('userRoles', 'Undefined one of Role');
                return;
            }
        }
    }

    public function userDepartmentsValidate(): void
    {
        $departments = array_keys($this->getDepartments());
        foreach ($this->userDepartments as $department) {
            if (!in_array((int)$department, $departments, true)) {
                $this->addError('userDepartments', 'Undefined one of Department');
                return;
            }
        }
    }

    public function clientChatChanelsValidate(): void
    {
        $chanels = array_keys($this->getClientChatChanels());
        foreach ($this->userClientChatChanels as $chanel) {
            if (!in_array((int)$chanel, $chanels, true)) {
                $this->addError('clientChatChanels', 'Undefined one of Client Chat Chanels');
                return;
            }
        }
    }
}
