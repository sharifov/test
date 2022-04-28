<?php

namespace modules\user\src\update;

use common\models\Employee;
use yii\base\Model;

/**
 * Class MultipleUpdateForm
 *
 * @property Employee $updaterUser
 * @property FieldAccess $fieldAccess
 * @property AvailableList $availableList
 */
class MultipleUpdateForm extends Model
{
    public $user_list;
    public $user_list_json;

    public $status;

    public $form_roles;
    public $user_departments;
    public $client_chat_user_channel;

    public $up_work_start_tm;
    public $up_work_minutes;
    public $up_timezone;
    public $up_base_amount;
    public $up_commission_percent;
    public $up_leaderboard_enabled;
    public $up_inbox_show_limit_leads;
    public $up_default_take_limit_leads;
    public $up_min_percent_for_take_leads;
    public $up_frequency_minutes;
    public $up_call_expert_limit;

    public $up_auto_redial;
    public $up_kpi_enable;

    public Employee $updaterUser;
    public FieldAccess $fieldAccess;
    public AvailableList $availableList;

    public function __construct(Employee $updaterUser, $config = [])
    {
        $this->updaterUser = $updaterUser;
        $this->fieldAccess = new FieldAccess($updaterUser, false);
        $this->availableList = new AvailableList($updaterUser);

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['user_list_json', 'required'],
            ['user_list_json', 'safe'],
            ['user_list_json', 'filter', 'filter' => function ($value) {
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

    public function attributeLabels(): array
    {
        return [
            'user_list' => 'Selected Users',
            'user_list_json' => 'Selected Users JSON',
            'status' => 'Status',
            'form_roles' => 'Roles',
            'user_departments' => 'Departments',
            'client_chat_user_channel' => 'Client Chat Channels',
            'up_work_start_tm' => 'Work Start Time',
            'up_work_minutes' => 'Work minutes',
            'up_timezone' => 'Timezone',
            'up_base_amount' => 'Base amount',
            'up_commission_percent' => 'Commission percent',
            'up_leaderboard_enabled' => 'Leader Board Enabled',
            'up_inbox_show_limit_leads' => 'Inbox show limit leads',
            'up_default_take_limit_leads' => 'Default take limit leads',
            'up_min_percent_for_take_leads' => 'Min percent for take leads',
            'up_frequency_minutes' => 'Take Frequency Minutes',
            'up_call_expert_limit'  => 'Call Expert Limit',
            'up_auto_redial' => 'Auto redial',
            'up_kpi_enable' => 'KPI enable',
        ];
    }
}
