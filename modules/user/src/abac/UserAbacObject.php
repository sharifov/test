<?php

namespace modules\user\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

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

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::USER_FORM => [
            self::ATTR_IS_NEW_RECORD
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
            'form_roles' => 'Roles',
            'status' => 'Status',
            'user_groups' => 'User Groups',
            'user_projects' => 'Projects access',
            'user_departments' => 'Departments',
            'client_chat_user_channel' => 'Client chat user channel',
            'up_work_start_tm' => 'Work Start Time',
            'up_work_minutes' => 'Work Minutes',
            'up_timezone' => 'Timezone',
            'up_base_amount' => 'Base Amount',
            'up_commission_percent' => 'Commission Percent',
            'up_bonus_active' => 'Bonus Is Active',
            'up_leaderboard_enabled' => 'Leader Board Enabled',
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
            'user_shift_assigns' => 'User shift assigns',
            'up_inbox_show_limit_leads' => 'Inbox show limit leads',
            'up_default_take_limit_leads' => 'Default take limit leads',
            'up_min_percent_for_take_leads' => 'Min percent for take leads',
            'up_frequency_minutes' => 'Take Frequency minutes',
            'up_call_expert_limit' => 'Call expert limit',
            'up_call_user_level' => 'Call priority level',
            'acl_rules_activated' => 'Acl rules activated',

        ];

        $attrFieldsList['values'] = $formFields;
        $attrMultiFieldsList['values'] = $formFields;

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        $attributeList[self::USER_FORM][] = $attrFieldsList;
        $attributeList[self::USER_FORM][] = $attrMultiFieldsList;

        $attributeList[self::USER_FEEDBACK][] = $attrFieldsList;
        $attributeList[self::USER_FEEDBACK][] = $attrMultiFieldsList;

        return $attributeList;
    }
}
