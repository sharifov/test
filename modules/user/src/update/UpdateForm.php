<?php

namespace modules\user\src\update;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use common\models\UserParams;
use common\models\UserProfile;
use yii\base\Model;

/**
 * Class UpdateForm
 *
 * @property string $username
 * @property string $email
 * @property string $full_name
 * @property string $password
 * @property string $nickname
 * @property int $status
 * @property boolean $acl_rules_activated
 *
 * @property $form_roles
 * @property $user_groups
 * @property $user_projects
 * @property $user_departments
 * @property $client_chat_user_channel
 * @property $user_shift_assigns
 *
 * @property $up_work_start_tm
 * @property $up_work_minutes
 * @property $up_timezone
 * @property $up_base_amount
 * @property $up_commission_percent
 * @property $up_bonus_active
 * @property $up_leaderboard_enabled
 * @property $up_inbox_show_limit_leads
 * @property $up_default_take_limit_leads
 * @property $up_min_percent_for_take_leads
 * @property $up_frequency_minutes
 * @property $up_call_expert_limit
 * @property $up_call_user_level
 *
 * @property $up_join_date
 * @property $up_skill
 * @property $up_call_type_id
 * @property $up_2fa_secret
 * @property $up_2fa_enable
 * @property $up_telegram
 * @property $up_telegram_enable
 * @property $up_auto_redial
 * @property $up_kpi_enable
 * @property $up_show_in_contact_list
 * @property $up_call_recording_disabled
 *
 * @property Employee $targetUser
 * @property Employee $updaterUser
 * @property FieldAccess $fieldAccess
 * @property AvailableList $availableList
 */
class UpdateForm extends Model
{
    public $username;
    public $email;
    public $full_name;
    public $password;
    public $nickname;
    public $status;
    public $acl_rules_activated;

    public $form_roles;
    public $user_groups;
    public $user_projects;
    public $user_departments;
    public $client_chat_user_channel;
    public $user_shift_assigns;

    public $up_work_start_tm;
    public $up_work_minutes;
    public $up_timezone;
    public $up_base_amount;
    public $up_commission_percent;
    public $up_bonus_active;
    public $up_leaderboard_enabled;
    public $up_inbox_show_limit_leads;
    public $up_default_take_limit_leads;
    public $up_min_percent_for_take_leads;
    public $up_frequency_minutes;
    public $up_call_expert_limit;
    public $up_call_user_level;

    public $up_join_date;
    public $up_skill;
    public $up_call_type_id;
    public $up_2fa_secret;
    public $up_2fa_enable;
    public $up_telegram;
    public $up_telegram_enable;
    public $up_auto_redial;
    public $up_kpi_enable;
    public $up_show_in_contact_list;
    public $up_call_recording_disabled;

    public Employee $targetUser;
    public Employee $updaterUser;
    public FieldAccess $fieldAccess;
    public AvailableList $availableList;

    public function __construct(Employee $targetUser, Employee $updaterUser, UserParams $userParams, UserProfile $userProfile, $config = [])
    {
        $this->targetUser = $targetUser;
        $this->updaterUser = $updaterUser;
        $this->fieldAccess = new FieldAccess($updaterUser, false);
        $this->availableList = new AvailableList($updaterUser);

        $this->setAttributes($targetUser->getAttributes(), false);
        $this->password = null;

        $this->form_roles = $targetUser->getRelations()->getRoles();
        $this->user_groups = $targetUser->getRelations()->getGroups();
        $this->user_projects = $targetUser->getRelations()->getProjects();
        $this->user_departments = $targetUser->getRelations()->getDepartments();
        $this->client_chat_user_channel = $targetUser->getRelations()->getClientChatChannels();
        $this->user_shift_assigns = $targetUser->getRelations()->getShiftAssigns();

        $this->setAttributes($userParams->getAttributes(), false);
        $this->setAttributes($userProfile->getAttributes(), false);

        parent::__construct($config);
    }

    public function isChangedRoles(): bool
    {
        if (count($this->targetUser->getRelations()->getRoles()) !== count($this->form_roles)) {
            return true;
        }
        foreach ($this->targetUser->getRelations()->getRoles() as $key => $name) {
            if (!in_array($key, $this->form_roles, true)) {
                return true;
            }
        }
        return false;
    }

    public function isChangedGroups(): bool
    {
        if (count($this->targetUser->getRelations()->getGroups()) !== count($this->user_groups)) {
            return true;
        }
        foreach ($this->targetUser->getRelations()->getGroups() as $key => $name) {
            if (!in_array($key, $this->user_groups, true)) {
                return true;
            }
        }
        return false;
    }

    public function isChangedDepartments(): bool
    {
        if (count($this->targetUser->getRelations()->getDepartments()) !== count($this->user_departments)) {
            return true;
        }
        foreach ($this->targetUser->getRelations()->getDepartments() as $key => $name) {
            if (!in_array($key, $this->user_departments, true)) {
                return true;
            }
        }
        return false;
    }

    public function isChangedProjects(): bool
    {
        if (count($this->targetUser->getRelations()->getProjects()) !== count($this->user_projects)) {
            return true;
        }
        foreach ($this->targetUser->getRelations()->getProjects() as $key => $name) {
            if (!in_array($key, $this->user_projects, true)) {
                return true;
            }
        }
        return false;
    }

    public function isChangedClientChatsChannels(): bool
    {
        if (count($this->targetUser->getRelations()->getClientChatChannels()) !== count($this->client_chat_user_channel)) {
            return true;
        }
        foreach ($this->targetUser->getRelations()->getClientChatChannels() as $key => $name) {
            if (!in_array($key, $this->client_chat_user_channel, true)) {
                return true;
            }
        }
        return false;
    }

    public function isChangedUserShiftAssign(): bool
    {
        if (count($this->targetUser->getRelations()->getShiftAssigns()) !== count($this->user_shift_assigns)) {
            return true;
        }
        foreach ($this->targetUser->getRelations()->getShiftAssigns() as $key => $name) {
            if (!in_array($key, $this->user_shift_assigns, true)) {
                return true;
            }
        }
        return false;
    }

    public function getValuesOfAvailableAttributes(): array
    {
        return $this->getAttributes($this->activeAttributes());
    }

    public function scenarios(): array
    {
        $attributes = [
            'username' => $this->fieldAccess->canEditUsername(),
            'email' => $this->fieldAccess->canEditEmail(),
            'full_name' => $this->fieldAccess->canEditFullName(),
            'password' => $this->fieldAccess->canEditPassword(),
            'nickname' => $this->fieldAccess->canEditNickname(),
            'status' => $this->fieldAccess->canEditStatus(),
            'acl_rules_activated' => $this->fieldAccess->canEditAclRulesActivated(),
            'form_roles' => $this->fieldAccess->canEditRoles(),
            'user_groups' => $this->fieldAccess->canEditUserGroups(),
            'user_projects' => $this->fieldAccess->canEditProjects(),
            'user_departments' => $this->fieldAccess->canEditDepartments(),
            'client_chat_user_channel' => $this->fieldAccess->canEditClientChatUserChannels(),
            'user_shift_assigns' => $this->fieldAccess->canEditUserShiftAssign(),
            'up_work_start_tm' => $this->fieldAccess->canEditWorkStartTime(),
            'up_work_minutes' => $this->fieldAccess->canEditWorkMinutes(),
            'up_timezone' => $this->fieldAccess->canEditTimeZone(),
            'up_base_amount' => $this->fieldAccess->canEditBaseAmount(),
            'up_commission_percent' => $this->fieldAccess->canEditCommissionPercent(),
            'up_bonus_active' => $this->fieldAccess->canEditBonusActive(),
            'up_leaderboard_enabled' => $this->fieldAccess->canEditLeaderboardEnabled(),
            'up_inbox_show_limit_leads' => $this->fieldAccess->canEditInboxShowLimitLeads(),
            'up_default_take_limit_leads' => $this->fieldAccess->canEditDefaultTakeLimitLeads(),
            'up_min_percent_for_take_leads' => $this->fieldAccess->canEditMinPercentForTakeLeads(),
            'up_frequency_minutes' => $this->fieldAccess->canEditFrequencyMinutes(),
            'up_call_expert_limit' => $this->fieldAccess->canEditCallExpertLimit(),
            'up_call_user_level' => $this->fieldAccess->canEditCallUserLevel(),
            'up_join_date' => $this->fieldAccess->canEditJoinDate(),
            'up_skill' => $this->fieldAccess->canEditSkill(),
            'up_call_type_id' => $this->fieldAccess->canEditCallTypeId(),
            'up_2fa_secret' => $this->fieldAccess->canEdit2faSecret(),
            'up_2fa_enable' => $this->fieldAccess->canEdit2faEnable(),
            'up_telegram' => $this->fieldAccess->canEditTelegram(),
            'up_telegram_enable' => $this->fieldAccess->canEditTelegramEnable(),
            'up_auto_redial' => $this->fieldAccess->canEditAutoRedial(),
            'up_kpi_enable' => $this->fieldAccess->canEditKpiEnable(),
            'up_show_in_contact_list' => $this->fieldAccess->canEditShowInContactList(),
            'up_call_recording_disabled' => $this->fieldAccess->canEditCallRecordingDisabled(),
        ];
        return [self::SCENARIO_DEFAULT => array_keys(array_filter($attributes, static fn ($v) => $v === true))];
    }

    public function rules(): array
    {
        return [
            ['username', 'required', 'when' => fn () => $this->fieldAccess->canEditUsername()],
            ['username', 'trim'],
            ['username', 'string', 'min' => 3, 'max' => 50],
            ['username', 'match' ,'pattern' => '/^[a-z0-9_\-\.]+$/i', 'message' => 'Username can contain only characters ("a-z", "0-9", "_", "-", ".")'],
            ['username', 'validateUniqueUsername', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['email', 'required', 'when' => fn () => $this->fieldAccess->canEditEmail()],
            ['email', 'trim'],
            ['email', 'string', 'max' => 255],
            ['email', 'filter', 'filter' => 'strtolower', 'skipOnEmpty' => true],
            ['email', 'validateUniqueEmail', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['full_name', 'required', 'when' => fn () => $this->fieldAccess->canEditFullName()],
            ['full_name', 'trim'],
            ['full_name', 'string', 'min' => 3, 'max' => 50],

            ['password', 'default', 'value' => null],
            ['password', 'string', 'min' => 8],

            ['nickname', 'required', 'when' => fn () => $this->fieldAccess->canEditNickname()],
            ['nickname', 'trim'],
            ['nickname', 'string', 'min' => 3, 'max' => 50],

            ['status', 'required', 'when' => fn () => $this->fieldAccess->canEditStatus()],
            ['status', 'filter', 'filter' => 'intval', 'skipOnError' => true, 'skipOnEmpty' => true],
            ['status', 'integer'],
            ['status', 'in', 'range' => array_keys($this->availableList->getStatuses())],

            ['acl_rules_activated', 'default', 'value' => null],
            ['acl_rules_activated', 'boolean'],
            ['acl_rules_activated', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['form_roles', 'default', 'value' => []],
            ['form_roles', 'required', 'when' => fn () => $this->fieldAccess->canEditRoles()],
            ['form_roles', IsArrayValidator::class],
            ['form_roles', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getRoles())]],

            ['user_groups', 'default', 'value' => []],
            ['user_groups', IsArrayValidator::class],
            ['user_groups', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['user_groups', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getUserGroups())]],

            ['user_projects', 'default', 'value' => []],
            ['user_projects', IsArrayValidator::class],
            ['user_projects', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['user_projects', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getProjects())]],

            ['user_departments', 'default', 'value' => []],
            ['user_departments', IsArrayValidator::class],
            ['user_departments', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['user_departments', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getDepartments())]],

            ['client_chat_user_channel', 'default', 'value' => []],
            ['client_chat_user_channel', IsArrayValidator::class],
            ['client_chat_user_channel', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['client_chat_user_channel', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getClientChatUserChannels())]],

            ['user_shift_assigns', 'default', 'value' => []],
            ['user_shift_assigns', IsArrayValidator::class],
            ['user_shift_assigns', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['user_shift_assigns', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getUserShiftAssign())]],

            ['up_work_start_tm', 'default', 'value' => null],
            ['up_work_start_tm', 'required', 'when' => fn () => $this->fieldAccess->canEditWorkStartTime()],
            ['up_work_start_tm', 'time', 'format' => 'php:H:i'],
            ['up_work_start_tm', 'filter', 'filter' => fn ($v) => $v . ':00', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['up_work_minutes', 'default', 'value' => null],
            ['up_work_minutes', 'required', 'when' => fn () => $this->fieldAccess->canEditWorkMinutes()],
            ['up_work_minutes', 'integer'],
            ['up_work_minutes', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_timezone', 'default', 'value' => null],
            ['up_timezone', 'required', 'when' => fn () => $this->fieldAccess->canEditTimeZone()],
            ['up_timezone', 'in', 'range' => array_keys($this->availableList->getTimezones())],

            ['up_base_amount', 'default', 'value' => null],
            ['up_base_amount', 'number'],

            ['up_commission_percent', 'default', 'value' => null],
            ['up_commission_percent', 'integer'],
            ['up_commission_percent', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_bonus_active', 'default', 'value' => null],
            ['up_bonus_active', 'boolean'],
            ['up_bonus_active', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_leaderboard_enabled', 'default', 'value' => null],
            ['up_leaderboard_enabled', 'boolean'],
            ['up_leaderboard_enabled', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_inbox_show_limit_leads', 'default', 'value' => null],
            ['up_inbox_show_limit_leads', 'integer'],
            ['up_inbox_show_limit_leads', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

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

            ['up_call_user_level', 'default', 'value' => 0],
            ['up_call_user_level', 'integer', 'min' => -128, 'max' => 127],
            ['up_call_user_level', 'filter', 'filter' => 'intval', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['up_join_date', 'default', 'value' => null],
            ['up_join_date', 'date', 'format' => 'php:Y-m-d'],

            ['up_skill', 'default', 'value' => null],
            ['up_skill', 'integer'],
            ['up_skill', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            ['up_skill', 'in', 'range' => array_keys($this->availableList->getSkillTypes())],

            ['up_call_type_id', 'default', 'value' => null],
            ['up_call_type_id', 'integer'],
            ['up_call_type_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            ['up_call_type_id', 'in', 'range' => array_keys($this->availableList->getCallTypes())],

            ['up_2fa_secret', 'default', 'value' => null],
            ['up_2fa_secret', 'string', 'max' => 50],

            ['up_2fa_enable', 'default', 'value' => null],
            ['up_2fa_enable', 'boolean'],
            ['up_2fa_enable', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_telegram', 'default', 'value' => null],
            ['up_telegram', 'string', 'max' => 20],

            ['up_telegram_enable', 'default', 'value' => null],
            ['up_telegram_enable', 'boolean'],
            ['up_telegram_enable', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_auto_redial', 'default', 'value' => null],
            ['up_auto_redial', 'boolean'],
            ['up_auto_redial', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_kpi_enable', 'default', 'value' => null],
            ['up_kpi_enable', 'boolean'],
            ['up_kpi_enable', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_show_in_contact_list', 'default', 'value' => null],
            ['up_show_in_contact_list', 'boolean'],
            ['up_show_in_contact_list', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['up_call_recording_disabled', 'default', 'value' => null],
            ['up_call_recording_disabled', 'boolean'],
            ['up_call_recording_disabled', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
    }

    public function validateUniqueUsername()
    {
        if (Employee::find()->andWhere(['username' => $this->username])->andWhere(['<>', 'id', $this->targetUser->id])->exists()) {
            $this->addError('username', 'Username is already exist');
        }
    }

    public function validateUniqueEmail()
    {
        if (Employee::find()->andWhere(['email' => $this->email])->andWhere(['<>', 'id', $this->targetUser->id])->exists()) {
            $this->addError('email', 'Email is already exist');
        }
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'full_name' => 'Full Name',
            'password' => 'Password',
            'nickname' => 'Nickname',
            'status' => 'Status',
            'acl_rules_activated' => 'Acl Rules Activated',
            'form_roles' => 'Roles',
            'user_groups' => 'User groups',
            'user_projects' => 'Projects access',
            'user_departments' => 'Departments',
            'client_chat_user_channel' => 'Client chat user channel',
            'user_shift_assigns' => 'User Shift Assign',
            'up_work_start_tm' => 'Work Start Time',
            'up_work_minutes' => 'Work Minutes',
            'up_timezone' => 'Timezone',
            'up_base_amount' => 'Base Amount',
            'up_commission_percent' => 'Commission Percent',
            'up_bonus_active' => 'Bonus Is Active',
            'up_leaderboard_enabled' => 'Leader Board Enabled',
            'up_inbox_show_limit_leads' => 'Inbox show limit leads',
            'up_default_take_limit_leads' => 'Default take limit leads',
            'up_min_percent_for_take_leads' => 'Min percent for take leads',
            'up_frequency_minutes' => 'Take Frequency Minutes',
            'up_call_expert_limit' => 'Call Expert limit',
            'up_call_user_level' => 'Call Priority Level',
            'up_join_date' => 'Join Date',
            'up_skill' => 'Skill',
            'up_call_type_id' => 'Call Type',
            'up_2fa_secret' => '2fa secret',
            'up_2fa_enable' => '2fa enable',
            'up_telegram' => 'Telegram ID',
            'up_telegram_enable' => 'Telegram Enable',
            'up_auto_redial' => 'Auto redial',
            'up_kpi_enable' => 'KPI enable',
            'up_show_in_contact_list' => 'Show in contact list',
            'up_call_recording_disabled' => 'Call recording disabled',
        ];
    }
}
