<?php

namespace modules\user\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use yii\helpers\ArrayHelper;

class UserAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'user/user/';

    /** OBJECT PERMISSION */
    public const USER_FORM = self::NS . 'form/user_update';

    public const USER_FEEDBACK = self::NS . 'obj/user-feedback';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::USER_FORM => self::USER_FORM,
        self::USER_FEEDBACK => self::USER_FEEDBACK
    ];

    /** --------------- ACTIONS ------------------------------- */
    public const ACTION_CREATE = 'create';
    public const ACTION_VIEW  = 'view';
    public const ACTION_EDIT  = 'edit';
    public const ACTION_MULTIPLE_UPDATE = 'multipleUpdate';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::USER_FORM => [self::ACTION_VIEW, self::ACTION_EDIT],
        self::USER_FEEDBACK => [self::ACTION_MULTIPLE_UPDATE, self::ACTION_CREATE]
    ];

    protected const ATTR_IS_NEW_RECORD = [
        'optgroup' => 'DB FLAGS',
        'id' => self::NS . 'isNewRecord',
        'field' => 'isNewRecord',
        'label' => 'Is New Record',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        //'validation' => ['allow_empty_value' => true],
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_FIELD_NAME = [
        'optgroup' => 'Form',
        'id' => self::NS . 'formAttribute',
        'field' => 'formAttribute',
        'label' => 'Field',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2]
    ];

    protected const ATTR_MULTI_FIELD_NAME = [
        'optgroup' => 'Form',
        'id' => self::NS . 'formMultiAttribute',
        'field' => 'formMultiAttribute',
        'label' => 'Multiple Field',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_CONTAINS]
    ];

    protected const ATTR_TARGET_USER_SAME_USER = [
        'optgroup' => 'Target User',
        'id' => self::NS . 'targetUserIsSameUser',
        'field' => 'targetUserIsSameUser',
        'label' => 'Is same user',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_TARGET_USER_SAME_GROUP = [
        'optgroup' => 'Target User',
        'id' => self::NS . 'targetUserIsSameGroup',
        'field' => 'targetUserIsSameGroup',
        'label' => 'Is same group',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_TARGET_USER_SAME_DEPARTMENT = [
        'optgroup' => 'Target User',
        'id' => self::NS . 'targetUserIsSameDepartment',
        'field' => 'targetUserIsSameDepartment',
        'label' => 'Is same department ',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_TARGET_USER_USERNAME = [
        'optgroup' => 'Target User',
        'id' => self::NS . 'targetUserUsername',
        'field' => 'targetUserUsername',
        'label' => 'Target user username',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2]
    ];

    protected const ATTR_TARGET_USER_ROLES = [
        'optgroup' => 'Target User',
        'id' => self::NS . 'targetUserRoles',
        'field' => 'targetUserRoles',
        'label' => 'Target user roles',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    protected const ATTR_TARGET_USER_PROJECTS = [
        'optgroup' => 'Target User',
        'id' => self::NS . 'targetUserProjects',
        'field' => 'targetUserProjects',
        'label' => 'Target user projects',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    protected const ATTR_TARGET_USER_GROUPS = [
        'optgroup' => 'Target User',
        'id' => self::NS . 'targetUserGroups',
        'field' => 'targetUserGroups',
        'label' => 'Target user groups',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    protected const ATTR_TARGET_USER_DEPARTMENTS = [
        'optgroup' => 'Target User',
        'id' => self::NS . 'targetUserDepartments',
        'field' => 'targetUserDepartments',
        'label' => 'Target user departments',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    protected const ATTR_SELECTED_ROLES = [
        'optgroup' => 'Form',
        'id' => self::NS . 'selectedRoles',
        'field' => 'selectedRoles',
        'label' => 'Selected Roles',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' => [self::OP_CONTAINS]
    ];


    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::USER_FORM => [
            self::ATTR_IS_NEW_RECORD,
            self::ATTR_TARGET_USER_SAME_GROUP,
            self::ATTR_TARGET_USER_SAME_USER,
            self::ATTR_TARGET_USER_SAME_DEPARTMENT,
            self::ATTR_TARGET_USER_USERNAME,
        ]
    ];

    public static function getObjectList(): array
    {
        return self::OBJECT_LIST;
    }

    public static function getObjectActionList(): array
    {
        return self::OBJECT_ACTION_LIST;
    }

    public static function getObjectAttributeList(): array
    {
        $attrFieldsList = self::ATTR_FIELD_NAME;
        $attrMultiFieldsList = self::ATTR_MULTI_FIELD_NAME;

        $formFields = [
            'username' => 'Username',
            'email' => 'Email',
            'full_name' => 'Full Name',
            'password' => 'Password',
            'nickname' => 'Nickname',
            'status' => 'Status',
            'acl_rules_activated' => 'Acl rules activated',
            'form_roles' => 'Roles',
            'user_groups' => 'User Groups',
            'user_projects' => 'Projects access',
            'user_departments' => 'Departments',
            'client_chat_user_channel' => 'Client chat user channel',
            'user_shift_assigns' => 'User shift assigns',
            'up_work_start_tm' => 'Work Start Time',
            'up_work_minutes' => 'Work Minutes',
            'up_timezone' => 'Timezone',
            'up_base_amount' => 'Base Amount',
            'up_commission_percent' => 'Commission Percent',
            'up_bonus_active' => 'Bonus Is Active',
            'up_leaderboard_enabled' => 'Leader Board Enabled',
            'up_inbox_show_limit_leads' => 'Inbox show limit leads',
            'up_business_inbox_show_limit_leads' => 'Business Inbox show limit leads',
            'up_default_take_limit_leads' => 'Default take limit leads',
            'up_min_percent_for_take_leads' => 'Min percent for take leads',
            'up_frequency_minutes' => 'Take Frequency minutes',
            'up_call_expert_limit' => 'Call expert limit',
            'up_call_user_level' => 'Call priority level',
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

        $attrFieldsList['values'] = $formFields;
        $attrMultiFieldsList['values'] = $formFields;

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        $targetUserRoles = self::ATTR_TARGET_USER_ROLES;
        $targetUserRoles['values'] = self::getUserRoleList();
        $attributeList[self::USER_FORM][] = $targetUserRoles;

        $targetUserProjects = self::ATTR_TARGET_USER_PROJECTS;
        $targetUserProjects['values'] = self::getProjectList();
        $attributeList[self::USER_FORM][] = $targetUserProjects;

        $targetUserGroups = self::ATTR_TARGET_USER_GROUPS;
        $targetUserGroups['values'] = self::getUserGroupList();
        $attributeList[self::USER_FORM][] = $targetUserGroups;

        $targetUserDepartments = self::ATTR_TARGET_USER_DEPARTMENTS;
        $targetUserDepartments['values'] = self::getDepartmentList();
        $attributeList[self::USER_FORM][] = $targetUserDepartments;

        $attributeList[self::USER_FORM][] = $attrFieldsList;
        $attributeList[self::USER_FORM][] = $attrMultiFieldsList;

        $attrSelectedRoles = self::ATTR_SELECTED_ROLES;
        $attrSelectedRoles['values'] = ArrayHelper::map(\Yii::$app->authManager->getRoles(), 'name', 'description');
        $attributeList[self::USER_FORM][] = $attrSelectedRoles;

        $attributeList[self::USER_FEEDBACK][] = $attrFieldsList;
        $attributeList[self::USER_FEEDBACK][] = $attrMultiFieldsList;

        return $attributeList;
    }
}
