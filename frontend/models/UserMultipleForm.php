<?php

namespace frontend\models;

use common\models\Employee;
use yii\base\Model;

/**
 * UserMultipleForm form
 */
class UserMultipleForm extends Model
{
    public $user_list;
    public $user_list_json;
    public $up_call_expert_limit;
    public $userDepartment;
    public $userRole;
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userRole', 'status'], 'string'],
            [['user_list_json'], 'required'],
            [['baseAmount', 'commissionPercent'], 'number'],
            [['up_call_expert_limit', 'status_id', 'inboxShowLimitLeads', 'defaultTakeLimitLeads', 'minPercentForTakeLeads', 'frequencyMinutes'], 'integer'],
            [['user_list_json', 'userDepartment', 'userRole', 'workStart', 'workMinutes', 'timeZone', 'autoRedial', 'kpiEnable', 'leaderBoardEnabled'], 'safe'],
            [['user_list_json'], 'filter', 'filter' => function ($value) {
                try {
                    $data = \yii\helpers\Json::decode($value);

                    if(!is_array($data)) {
                        $this->addError('user_list_json', 'Invalid JSON data for decode');
                        return null;
                    }

                    foreach ($data as $userId) {
                        $model = Employee::findOne($userId);
                        if (!$model) {
                            $this->addError('user_list_json', 'Not found Employee ID: '.$userId);
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
            'userRole' => 'Role',
            'workStart' => 'Work Start Time',
            'timeZone' => 'Timezone',
            'frequencyMinutes' => 'Take Frequency Minutes',
            'autoRedial' => 'Auto redial',
            'kpiEnable' => 'KPI enable',
            'leaderBoardEnabled' => 'Leader Board Enabled'
        ];
    }
}
