<?php

namespace modules\user\src\update;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\user\src\abac\dto\UserAbacDto;
use modules\user\src\abac\UserAbacObject;
use src\model\clientChatChannel\entity\ClientChatChannel;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class UpdateForm
 *
 * @property int $userId
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
 * @property Employee $updater
 */
class UpdateForm extends Model
{
    public $userId;
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

    public Employee $updater;

    public function __construct(Employee $targetUser, Employee $updater, $config = [])
    {
        $this->updater = $updater;

        $this->userId = $targetUser->id;
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

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['username', 'required', 'when' => function () {
                return $this->canEditUsername();
            }],

            ['email', 'required', 'when' => function () {
                return $this->canEditEmail();
            }],

            ['full_name', 'required', 'when' => function () {
                return $this->canEditFullName();
            }],

            ['nickname', 'required', 'when' => function () {
                return $this->canEditNickname();
            }],

            ['status', 'required', 'when' => function () {
                return $this->canEditStatus();
            }],

            ['form_roles', 'required', 'when' => function () {
                return $this->canEditRoles();
            }],
            ['form_roles', IsArrayValidator::class],
            ['form_roles', 'each', 'rule' => ['in', 'range' => array_keys($this->getAvailableRoles())]],

            ['user_groups', IsArrayValidator::class],
            ['user_groups', 'each', 'rule' => ['in', 'range' => array_keys($this->getAvailableUserGroups())]],

            ['user_projects', IsArrayValidator::class],
            ['user_projects', 'each', 'rule' => ['in', 'range' => array_keys($this->getAvailableProjects())]],

            ['user_departments', IsArrayValidator::class],
            ['user_departments', 'each', 'rule' => ['in', 'range' => array_keys($this->getAvailableDepartments())]],

            ['client_chat_user_channel', IsArrayValidator::class],
            ['client_chat_user_channel', 'each', 'rule' => ['in', 'range' => array_keys($this->getAvailableClientChatUserChannels())]],

            ['user_shift_assigns', IsArrayValidator::class],
            ['user_shift_assigns', 'each', 'rule' => ['in', 'range' => array_keys($this->getAvailableUserShiftAssign())]],
        ];
    }

    public function canViewStatus(): bool
    {
        $userAbacDto = new UserAbacDto('status');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, Status field view*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->updater);
    }

    public function canEditStatus(): bool
    {
        $userAbacDto = new UserAbacDto('status');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, Status field edit*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->updater);
    }

    public function canViewNickname(): bool
    {
        $userAbacDto = new UserAbacDto('nickname');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, Nickname field view*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->updater);
    }

    public function canEditNickname(): bool
    {
        $userAbacDto = new UserAbacDto('nickname');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, Nickname field edit*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->updater);
    }

    public function canViewPassword(): bool
    {
        $userAbacDto = new UserAbacDto('password');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, Password field view*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->updater);
    }

    public function canEditPassword(): bool
    {
        $userAbacDto = new UserAbacDto('password');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, Password field edit*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->updater);
    }

    public function canViewFullName(): bool
    {
        $userAbacDto = new UserAbacDto('full_name');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, Full name field view*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->updater);
    }

    public function canEditFullName(): bool
    {
        $userAbacDto = new UserAbacDto('full_name');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, Full name field edit*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->updater);
    }

    public function canViewEmail(): bool
    {
        $userAbacDto = new UserAbacDto('email');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, Email field view*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->updater);
    }

    public function canEditEmail(): bool
    {
        $userAbacDto = new UserAbacDto('email');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, Email field edit*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->updater);
    }

    public function canViewUsername(): bool
    {
        $userAbacDto = new UserAbacDto('username');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, Username field view*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->updater);
    }

    public function canEditUsername(): bool
    {
        $userAbacDto = new UserAbacDto('username');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, Username field edit*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->updater);
    }

    public function getAvailableRoles(): array
    {
        //todo validate available roles for updater user
        return Employee::getAllRoles($this->updater);
    }

    public function canEditRoles(): bool
    {
        $userAbacDto = new UserAbacDto('form_roles');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, User Roles field edit*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->updater);
    }

    public function canViewRoles(): bool
    {
        $userAbacDto = new UserAbacDto('form_roles');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, User Roles field view*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->updater);
    }

    public function getAvailableUserGroups(): array
    {
        if ($this->updater->isAdmin() || $this->updater->isSuperAdmin() || $this->updater->isUserManager()) {
            return \common\models\UserGroup::getList();
        }

        if ($this->updater->isSupervision()) {
            return $this->updater->getUserGroupList();
        }

        return [];
    }

    public function canViewUserGroups(): bool
    {
        $userAbacDto = new UserAbacDto('user_groups');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, User Groups field view*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->updater);
    }

    public function canEditUserGroups(): bool
    {
        $userAbacDto = new UserAbacDto('user_groups');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, User Groups field edit*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->updater);
    }

    public function getAvailableProjects(): array
    {
        if ($this->updater->isAdmin() || $this->updater->isSuperAdmin() || $this->updater->isUserManager()) {
            return \common\models\Project::getList();
        }

        if ($this->updater->isSupervision()) {
            return \yii\helpers\ArrayHelper::map($this->updater->projects, 'id', 'name');
        }

        return [];
    }

    public function canViewProjects(): bool
    {
        $userAbacDto = new UserAbacDto('user_projects');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, User Projects field view*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->updater);
    }

    public function canEditProjects(): bool
    {
        $userAbacDto = new UserAbacDto('user_projects');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, User Projects field edit*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->updater);
    }

    public function getAvailableDepartments(): array
    {
        //todo validate available departments for updater user
        return \common\models\Department::getList();
    }

    public function canViewDepartments(): bool
    {
        $userAbacDto = new UserAbacDto('user_departments');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, User Departments field view*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->updater);
    }

    public function canEditDepartments(): bool
    {
        $userAbacDto = new UserAbacDto('user_departments');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, User Departments field edit*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->updater);
    }

    public function getAvailableClientChatUserChannels(): array
    {
        //todo validate available client chats for updater user
        return ClientChatChannel::getList();
    }

    public function canViewClientChatUserChannels(): bool
    {
        $userAbacDto = new UserAbacDto('client_chat_user_channel');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, User Client Chat User Channels field view*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->updater);
    }

    public function canEditClientChatUserChannels(): bool
    {
        $userAbacDto = new UserAbacDto('client_chat_user_channel');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, User Client Chat User Channels field edit*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->updater);
    }

    public function getAvailableUserShiftAssign(): array
    {
        //todo validate available shifts for updater user
        return Shift::getList();
    }

    public function canViewUserShiftAssign(): bool
    {
        $userAbacDto = new UserAbacDto('user_shift_assigns');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, User Shift Assigns field view*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->updater);
    }

    public function canEditUserShiftAssign(): bool
    {
        $userAbacDto = new UserAbacDto('user_shift_assigns');
        $userAbacDto->isNewRecord = false;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, User Shift Assigns field edit*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->updater);
    }
}
