<?php

namespace modules\user\src\update;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use common\models\UserProfile;
use src\model\clientChatChannel\entity\ClientChatChannel;
use yii\base\Model;

/**
 * Class MultipleUpdateForm
 *
 * @property $user_list
 * @property $user_list_json
 *
 * @property $status
 * @property $form_roles
 * @property $form_roles_action
 * @property array $user_departments
 * @property $user_departments_action
 * @property $client_chat_user_channel
 *
 * @property $up_work_start_tm
 * @property $up_work_minutes
 * @property $up_timezone
 * @property $up_base_amount
 * @property $up_commission_percent
 * @property $up_leaderboard_enabled
 * @property $up_inbox_show_limit_leads
 * @property $up_business_inbox_show_limit_leads
 * @property $up_default_take_limit_leads
 * @property $up_min_percent_for_take_leads
 * @property $up_frequency_minutes
 * @property $up_call_expert_limit
 * @property $up_auto_redial
 * @property $up_kpi_enable
 *
 * @property AvailableList $availableList
 */
class MultipleUpdateForm extends Model
{
    public $user_list;
    public $user_list_json;

    public $status;

    public $form_roles;
    public $form_roles_action;
    public $user_groups;
    public $user_groups_action;
    public $user_departments;
    public $user_departments_action;
    public $client_chat_user_channel;

    public $up_work_start_tm;
    public $up_work_minutes;
    public $up_timezone;
    public $up_base_amount;
    public $up_commission_percent;
    public $up_leaderboard_enabled;
    public $up_inbox_show_limit_leads;
    public $up_business_inbox_show_limit_leads;
    public $up_default_take_limit_leads;
    public $up_min_percent_for_take_leads;
    public $up_frequency_minutes;
    public $up_call_expert_limit;

    public $up_auto_redial;
    public $up_kpi_enable;
    public $up_skill;

    public AvailableList $availableList;

    public const GROUP_ADD = 1;
    public const GROUP_REPLACE = 2;
    public const GROUP_DELETE = 3;

    public const GROUPS_ACTION_LIST = [
        self::GROUP_ADD => 'Add',
        self::GROUP_REPLACE => 'Replace',
        self::GROUP_DELETE => 'Remove',
    ];

    public const ROLE_ADD = 1;
    public const ROLE_REPLACE   = 2;
    public const ROLE_REMOVE = 3;

    public const ROLES_ACTION_LIST = [
        self::ROLE_ADD => 'Add',
        self::ROLE_REPLACE => 'Replace',
        self::ROLE_REMOVE => 'Remove'
    ];

    public const DEPARTMENT_ADD = 1;
    public const DEPARTMENT_REPLACE   = 2;
    public const DEPARTMENT_REMOVE = 3;

    public const DEPARTMENTS_ACTION_LIST = [
        self::DEPARTMENT_ADD => 'Add',
        self::DEPARTMENT_REPLACE => 'Replace',
        self::DEPARTMENT_REMOVE => 'Remove'
    ];

    public function __construct(Employee $updaterUser, $config = [])
    {
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

            ['status', 'default', 'value' => null],
            ['status', 'integer'],
            ['status', 'filter', 'filter' => 'intval', 'skipOnError' => true, 'skipOnEmpty' => true],
            ['status', 'in', 'range' => array_keys($this->availableList->getStatuses())],

            ['form_roles', 'default', 'value' => []],
            ['form_roles', IsArrayValidator::class],
            ['form_roles', 'each', 'rule' => ['string'], 'skipOnEmpty' => true, 'skipOnError' => true],
            ['form_roles', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getRoles())], 'skipOnEmpty' => true, 'skipOnError' => true],

            ['form_roles_action', 'default', 'value' => self::ROLE_ADD],
            ['form_roles_action', 'integer'],
            ['form_roles_action', 'in', 'range' => array_keys(self::ROLES_ACTION_LIST)],

            ['user_groups', 'default', 'value' => []],
            ['user_groups', IsArrayValidator::class],
            ['user_groups', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getUserGroups())]],

            ['user_groups_action', 'default', 'value' => self::GROUP_ADD],
            ['user_groups_action', 'integer'],
            ['user_groups_action', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['user_departments', 'default', 'value' => []],
            ['user_departments', IsArrayValidator::class],
            ['user_departments', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['user_departments', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getDepartments())]],

            ['user_departments_action', 'default', 'value' => self::DEPARTMENT_ADD],
            ['user_departments_action', 'integer'],
            ['user_departments_action', 'in', 'range' => array_keys(self::DEPARTMENTS_ACTION_LIST)],

            ['client_chat_user_channel', 'default', 'value' => []],
            ['client_chat_user_channel', IsArrayValidator::class],
            ['client_chat_user_channel', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['client_chat_user_channel', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getClientChatUserChannels())]],

            ['up_work_start_tm', 'default', 'value' => null],
            ['up_work_start_tm', 'time', 'format' => 'php:H:i'],
            ['up_work_start_tm', 'filter', 'filter' => fn ($v) => $v . ':00', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['up_work_minutes', 'default', 'value' => null],
            ['up_work_minutes', 'integer'],
            ['up_work_minutes', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_timezone', 'default', 'value' => null],
            ['up_timezone', 'in', 'range' => array_keys($this->availableList->getTimezones())],

            ['up_base_amount', 'default', 'value' => null],
            ['up_base_amount', 'number'],

            ['up_commission_percent', 'default', 'value' => null],
            ['up_commission_percent', 'integer'],
            ['up_commission_percent', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_leaderboard_enabled', 'default', 'value' => null],
            ['up_leaderboard_enabled', 'boolean'],
            ['up_leaderboard_enabled', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_inbox_show_limit_leads', 'default', 'value' => null],
            ['up_inbox_show_limit_leads', 'integer'],
            ['up_inbox_show_limit_leads', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_business_inbox_show_limit_leads', 'default', 'value' => null],
            ['up_business_inbox_show_limit_leads', 'integer'],
            ['up_business_inbox_show_limit_leads', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_default_take_limit_leads', 'default', 'value' => null],
            ['up_default_take_limit_leads', 'integer'],
            ['up_default_take_limit_leads', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_min_percent_for_take_leads', 'default', 'value' => null],
            ['up_min_percent_for_take_leads', 'integer'],
            ['up_min_percent_for_take_leads', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_frequency_minutes', 'default', 'value' => null],
            ['up_frequency_minutes', 'integer'],
            ['up_frequency_minutes', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_call_expert_limit', 'default', 'value' => null],
            ['up_call_expert_limit', 'integer'],
            ['up_call_expert_limit', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_auto_redial', 'default', 'value' => null],
            ['up_auto_redial', 'boolean'],
            ['up_auto_redial', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_kpi_enable', 'default', 'value' => null],
            ['up_kpi_enable', 'boolean'],
            ['up_kpi_enable', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_skill', 'in', 'range' => array_keys(UserProfile::SKILL_TYPE_LIST), 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'user_list' => 'Selected Users',
            'user_list_json' => 'Selected Users JSON',
            'status' => 'Status',
            'form_roles' => 'Roles',
            'form_roles_action' => 'Roles Action',
            'user_groups' => 'Assign User Groups',
            'user_departments' => 'Departments',
            'user_departments_action' => 'Departments Action',
            'client_chat_user_channel' => 'Client Chat Channels',
            'up_work_start_tm' => 'Work Start Time',
            'up_work_minutes' => 'Work minutes',
            'up_timezone' => 'Timezone',
            'up_base_amount' => 'Base amount',
            'up_commission_percent' => 'Commission percent',
            'up_leaderboard_enabled' => 'Leader Board Enabled',
            'up_inbox_show_limit_leads' => 'Inbox show limit leads',
            'up_business_inbox_show_limit_leads' => 'Business Inbox show limit leads',
            'up_default_take_limit_leads' => 'Default take limit leads',
            'up_min_percent_for_take_leads' => 'Min percent for take leads',
            'up_frequency_minutes' => 'Take Frequency Minutes',
            'up_call_expert_limit'  => 'Call Expert Limit',
            'up_auto_redial' => 'Auto redial',
            'up_kpi_enable' => 'KPI enable',
            'up_skill' => 'Skill',
        ];
    }

    public function getChangedClientChatsChannels(): array
    {
        $clientChatChannel = ClientChatChannel::find()->where(['in', 'ccc_id', $this->client_chat_user_channel])->orderBy(['ccc_name' => SORT_ASC])->all();
        if ($clientChatChannel) {
            return \yii\helpers\ArrayHelper::map($clientChatChannel, 'ccc_id', 'ccc_name');
        }

        return [];
    }

    public function groupActionIsReplace(): bool
    {
        return $this->user_groups_action === self::GROUP_REPLACE;
    }
}
