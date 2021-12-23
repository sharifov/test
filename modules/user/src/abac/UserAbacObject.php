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

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::USER_FORM => self::USER_FORM
    ];

    /** --------------- ACTIONS ------------------------------- */
    public const ACTION_VIEW  = 'view';
    public const ACTION_EDIT  = 'edit';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::USER_FORM => [self::ACTION_VIEW, self::ACTION_EDIT]
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
        ];

        $attrFieldsList['values'] = $formFields;
        $attrMultiFieldsList['values'] = $formFields;

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        $attributeList[self::USER_FORM][] = $attrFieldsList;
        $attributeList[self::USER_FORM][] = $attrMultiFieldsList;

        return $attributeList;
    }
}
