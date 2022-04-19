<?php

namespace modules\user\src\update;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use common\models\UserParams;
use common\models\UserProfile;
use yii\base\Model;
use yii\helpers\ArrayHelper;

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

    public function __construct(Employee $targetUser, Employee $updaterUser, $config = [])
    {
        $this->targetUser = $targetUser;
        $this->updaterUser = $updaterUser;
        $this->fieldAccess = new FieldAccess($updaterUser, false);
        $this->availableList = new AvailableList($updaterUser);

        $this->username = $targetUser->username;
        $this->email = $targetUser->email;
        $this->full_name = $targetUser->full_name;
        $this->password = null;
        $this->nickname = $targetUser->nickname;
        $this->status = $targetUser->status;
        $this->acl_rules_activated = $targetUser->acl_rules_activated;

        $this->form_roles = ArrayHelper::map(\Yii::$app->authManager->getRolesByUser($targetUser->id), 'name', 'name');
        $this->user_groups = ArrayHelper::map($targetUser->userGroupAssigns, 'ugs_group_id', 'ugs_group_id');
        $this->user_projects = ArrayHelper::map($targetUser->projects, 'id', 'id');
        $this->user_departments = ArrayHelper::map($targetUser->userDepartments, 'ud_dep_id', 'ud_dep_id');
        $this->client_chat_user_channel = ArrayHelper::map($targetUser->clientChatUserChannel, 'ccuc_channel_id', 'ccuc_channel_id');
        $this->user_shift_assigns = ArrayHelper::map($targetUser->userShiftAssigns, 'usa_sh_id', 'usa_sh_id');

        if (!$userParams = $targetUser->userParams) {
            $userParams = new UserParams();
        }
        $this->setAttributes($userParams->getAttributes());

        if (!$profile = $targetUser->userProfile) {
            $profile = new UserProfile();
            $profile->up_join_date = date('Y-m-d');
        }
        $this->setAttributes($profile->getAttributes());

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['username', 'required', 'when' => function () {
                return $this->fieldAccess->canEditUsername();
            }],

            ['email', 'required', 'when' => function () {
                return $this->fieldAccess->canEditEmail();
            }],

            ['full_name', 'required', 'when' => function () {
                return $this->fieldAccess->canEditFullName();
            }],

            ['nickname', 'required', 'when' => function () {
                return $this->fieldAccess->canEditNickname();
            }],

            ['status', 'required', 'when' => function () {
                return $this->fieldAccess->canEditStatus();
            }],

            ['form_roles', 'required', 'when' => function () {
                return $this->fieldAccess->canEditRoles();
            }],
            ['form_roles', IsArrayValidator::class],
            ['form_roles', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getRoles())]],

            ['user_groups', IsArrayValidator::class],
            ['user_groups', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getUserGroups())]],

            ['user_projects', IsArrayValidator::class],
            ['user_projects', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getProjects())]],

            ['user_departments', IsArrayValidator::class],
            ['user_departments', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getDepartments())]],

            ['client_chat_user_channel', IsArrayValidator::class],
            ['client_chat_user_channel', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getClientChatUserChannels())]],

            ['user_shift_assigns', IsArrayValidator::class],
            ['user_shift_assigns', 'each', 'rule' => ['in', 'range' => array_keys($this->availableList->getUserShiftAssign())]],

            ['up_work_start_tm', 'safe'],
            ['up_work_minutes', 'safe'],
            ['up_timezone', 'safe'],
            ['up_base_amount', 'safe'],
            ['up_commission_percent', 'safe'],
            ['up_bonus_active', 'safe'],
            ['up_leaderboard_enabled', 'safe'],
            ['up_inbox_show_limit_leads', 'safe'],
            ['up_default_take_limit_leads', 'safe'],
            ['up_min_percent_for_take_leads', 'safe'],
            ['up_frequency_minutes', 'safe'],
            ['up_call_expert_limit', 'safe'],
            ['up_call_user_level', 'safe'],

            ['up_join_date', 'safe'],
            ['up_skill', 'safe'],
            ['up_call_type_id', 'safe'],
            ['up_2fa_secret', 'safe'],
            ['up_2fa_enable', 'safe'],
            ['up_telegram', 'safe'],
            ['up_telegram_enable', 'safe'],
            ['up_auto_redial', 'safe'],
            ['up_kpi_enable', 'safe'],
            ['up_show_in_contact_list', 'safe'],
            ['up_call_recording_disabled', 'safe'],
        ];
    }
}
