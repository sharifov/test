<?php

namespace modules\lead\src\abac;

use common\models\Department;
use common\models\Lead;
use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

/**
 * Class LeadAbacObject
 */
class LeadAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    public const NS = 'lead/lead/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_USER_CONVERSION  = self::NS . 'act/user-conversion';
    //public const ACT_CLIENT_DETAILS  = self::NS . 'act/client-details'; //TODO: To Remove 1
    //public const ACT_CLIENT_ADD_PHONE  = self::NS . 'act/client-add-phone'; //TODO: To Remove 2
    //public const ACT_CLIENT_EDIT_PHONE  = self::NS . 'act/client-edit-phone'; //TODO: To Remove 3
    //public const ACT_USER_SAME_PHONE_INFO  = self::NS . 'act/user-same-phone-info'; //TODO: To Remove 4
    //public const ACT_CLIENT_ADD_EMAIL  = self::NS . 'act/client-add-email'; //TODO: To Remove 5
    //public const ACT_CLIENT_EDIT_EMAIL  = self::NS . 'act/client-edit-email'; //TODO: To Remove 6
    //public const ACT_USER_SAME_EMAIL_INFO  = self::NS . 'act/user-same-email-info'; //TODO: To Remove 7
    public const ACT_TAKE_LEAD = self::NS . 'act/take-lead';
    //public const ACT_CLIENT_UPDATE  = self::NS . 'act/client-update'; //TODO: To Remove 8
    //public const ACT_CLIENT_SUBSCRIBE  = self::NS . 'act/client-subscribe'; //TODO: To Remove 9
    //public const ACT_CLIENT_UNSUBSCRIBE  = self::NS . 'act/client-unsubscribe'; //TODO: To Remove 10
    //public const ACT_SEARCH_LEADS_BY_IP  = self::NS . 'act/search-leads-by-ip'; //TODO: To Remove 11
    public const ACT_CREATE_FROM_PHONE_WIDGET = self::NS . 'act/create-from-phone-widget';
    public const ACT_LINK_TO_CALL = self::NS . 'act/link-to-call';
    public const ACT_TAKE_LEAD_FROM_CALL = self::NS . 'act/take-from-call';

    /** UI PERMISSION */
    public const UI_BLOCK_CLIENT_INFO  = self::NS . 'ui/block/client-info';
    //public const UI_MENU_CLIENT_INFO  = self::NS . 'ui/menu/client-info'; //TODO: To Remove
    //public const UI_FIELD_PHONE_FORM_ADD_PHONE = self::NS . 'ui/field/phone'; //TODO: To Remove 12
    public const PHONE_CREATE_FORM = self::NS . 'form/phone_create';
    public const EMAIL_CREATE_FORM = self::NS . 'form/email_create';
    public const CLIENT_CREATE_FORM = self::NS . 'form/client_create';
    //public const UI_FIELD_EMAIL_FORM_ADD_EMAIL = self::NS . 'ui/field/email'; //TODO: To Remove 13
    //public const UI_FIELD_LOCALE_FORM_UPDATE_CLIENT = self::NS . 'ui/field/locale'; //TODO: To Remove 14
    //public const UI_FIELD_MARKETING_COUNTRY = self::NS . 'ui/field/marketing_country'; //TODO: To Remove 15
    public const UI_DISPLAY_QUOTE_SEARCH_PARAMS = self::NS . 'ui/quote/search/params';

    /** LOGIC PERMISSION */
    public const LOGIC_CLIENT_DATA   = self::NS . 'logic/client_data';

    /** COMMAND PERMISSION */
    public const CMD_AUTO_REDIAL      = self::NS . 'cmd/auto_redial';

    /** QUERY PERMISSIONS */
    public const QUERY_SOLD_ALL = self::NS . 'query/sold/*';
    public const QUERY_SOLD_PROJECTS = self::NS . 'query/sold/projects';
    public const QUERY_SOLD_DEPARTMENTS = self::NS . 'query/sold/departments';
    public const QUERY_SOLD_GROUPS = self::NS . 'query/sold/groups';
    public const QUERY_SOLD_IS_OWNER = self::NS . 'query/sold/is_owner';
    public const QUERY_SOLD_IS_EMPTY_OWNER = self::NS . 'query/sold/is_empty_owner';

    /** OBJECT PERMISSION */
    public const OBJ_LEAD_PREFERENCES    = self::NS . 'obj/lead_preferences';
    public const OBJ_LEAD                = self::NS . 'obj/lead';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::ACT_USER_CONVERSION   => self::ACT_USER_CONVERSION,
        //self::ACT_CLIENT_DETAILS    => self::ACT_CLIENT_DETAILS,
        //self::ACT_CLIENT_ADD_PHONE    => self::ACT_CLIENT_ADD_PHONE,
        //self::ACT_CLIENT_EDIT_PHONE    => self::ACT_CLIENT_EDIT_PHONE,
        //self::ACT_USER_SAME_PHONE_INFO    => self::ACT_USER_SAME_PHONE_INFO,
        //self::ACT_CLIENT_ADD_EMAIL    => self::ACT_CLIENT_ADD_EMAIL,
        //self::ACT_CLIENT_EDIT_EMAIL    => self::ACT_CLIENT_EDIT_EMAIL,
        //self::ACT_USER_SAME_EMAIL_INFO    => self::ACT_USER_SAME_EMAIL_INFO,
        //self::ACT_CLIENT_UPDATE    => self::ACT_CLIENT_UPDATE,
        //self::ACT_CLIENT_SUBSCRIBE    => self::ACT_CLIENT_SUBSCRIBE,
        //self::ACT_CLIENT_UNSUBSCRIBE    => self::ACT_CLIENT_UNSUBSCRIBE,
        self::ACT_TAKE_LEAD => self::ACT_TAKE_LEAD,
        self::UI_BLOCK_CLIENT_INFO  => self::UI_BLOCK_CLIENT_INFO,
        //self::UI_MENU_CLIENT_INFO   => self::UI_MENU_CLIENT_INFO,
        //self::ACT_SEARCH_LEADS_BY_IP   => self::ACT_SEARCH_LEADS_BY_IP,
        self::LOGIC_CLIENT_DATA   => self::LOGIC_CLIENT_DATA,
        //self::UI_FIELD_PHONE_FORM_ADD_PHONE   => self::UI_FIELD_PHONE_FORM_ADD_PHONE,
        //self::UI_FIELD_EMAIL_FORM_ADD_EMAIL   => self::UI_FIELD_EMAIL_FORM_ADD_EMAIL,
        //self::UI_FIELD_LOCALE_FORM_UPDATE_CLIENT   => self::UI_FIELD_LOCALE_FORM_UPDATE_CLIENT,
        //self::UI_FIELD_MARKETING_COUNTRY   => self::UI_FIELD_MARKETING_COUNTRY,
        self::QUERY_SOLD_ALL   => self::QUERY_SOLD_ALL,
        self::QUERY_SOLD_PROJECTS   => self::QUERY_SOLD_PROJECTS,
        self::QUERY_SOLD_DEPARTMENTS   => self::QUERY_SOLD_DEPARTMENTS,
        self::QUERY_SOLD_GROUPS   => self::QUERY_SOLD_GROUPS,
        self::QUERY_SOLD_IS_OWNER   => self::QUERY_SOLD_IS_OWNER,
        self::QUERY_SOLD_IS_EMPTY_OWNER   => self::QUERY_SOLD_IS_EMPTY_OWNER,
        self::UI_DISPLAY_QUOTE_SEARCH_PARAMS => self::UI_DISPLAY_QUOTE_SEARCH_PARAMS,
        self::CMD_AUTO_REDIAL => self::CMD_AUTO_REDIAL,
        self::ACT_CREATE_FROM_PHONE_WIDGET => self::ACT_CREATE_FROM_PHONE_WIDGET,
        self::ACT_LINK_TO_CALL => self::ACT_LINK_TO_CALL,
        self::ACT_TAKE_LEAD_FROM_CALL => self::ACT_TAKE_LEAD_FROM_CALL,
        self::OBJ_LEAD_PREFERENCES => self::OBJ_LEAD_PREFERENCES,
        self::OBJ_LEAD => self::OBJ_LEAD,
        self::PHONE_CREATE_FORM => self::PHONE_CREATE_FORM,
        self::EMAIL_CREATE_FORM => self::EMAIL_CREATE_FORM,
        self::CLIENT_CREATE_FORM => self::CLIENT_CREATE_FORM,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_ACCESS_DETAILS  = 'accessDetails';
    public const ACTION_ACCESS_ADD_PHONE  = 'accessAddPhone';
    public const ACTION_ADD_PHONE  = 'addPhone';
    public const ACTION_ACCESS_EDIT_PHONE  = 'accessEditPhone';
    public const ACTION_EDIT_PHONE  = 'editPhone';
    public const ACTION_ACCESS_USER_SAME_PHONE = 'accessUserSamePhoneInfo';
    public const ACTION_ACCESS_ADD_EMAIL  = 'accessAddEmail';
    public const ACTION_ADD_EMAIL  = 'addEmail';
    public const ACTION_ACCESS_EDIT_EMAIL  = 'accessEditEmail';
    public const ACTION_EDIT_EMAIL  = 'editEmail';
    public const ACTION_ACCESS_USER_SAME_EMAIL = 'accessUserSameEmailInfo';
    public const ACTION_ACCESS_UPDATE_CLIENT = 'accessUpdateClient';
    public const ACTION_SUBSCRIBE  = 'subscribe';
    public const ACTION_UNSUBSCRIBE  = 'unsubscribe';
    public const ACTION_SHOW_LEADS_BY_IP  = 'showLeadsByIp';
    public const ACTION_CREATE  = 'create';
    public const ACTION_READ    = 'read';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_DELETE  = 'delete';
    public const ACTION_UNMASK  = 'unmask';
    public const ACTION_QUERY_AND  = 'and';
    public const ACTION_QUERY_OR  = 'or';
    public const ACTION_SET_DELAY_CHARGE  = 'setDelayedCharge';
    public const ACTION_CLONE = 'clone';
    public const ACTION_EDIT = 'edit';
    public const ACTION_VIEW = 'view';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_USER_CONVERSION  => [self::ACTION_READ, self::ACTION_DELETE, self::ACTION_CREATE],
        //self::ACT_CLIENT_DETAILS => [self::ACTION_ACCESS],
        //self::ACT_CLIENT_ADD_PHONE => [self::ACTION_ACCESS, self::ACTION_CREATE],
        //self::ACT_CLIENT_EDIT_PHONE => [self::ACTION_ACCESS, self::ACTION_UPDATE],
        //self::ACT_USER_SAME_PHONE_INFO => [self::ACTION_ACCESS],
        //self::ACT_CLIENT_ADD_EMAIL => [self::ACTION_ACCESS, self::ACTION_CREATE],
        //self::ACT_CLIENT_EDIT_EMAIL => [self::ACTION_ACCESS, self::ACTION_UPDATE],
        //self::ACT_USER_SAME_EMAIL_INFO => [self::ACTION_ACCESS],
        //self::ACT_CLIENT_UPDATE => [self::ACTION_ACCESS],
        self::UI_BLOCK_CLIENT_INFO => [
            self::ACTION_ACCESS,
            self::ACTION_ACCESS_DETAILS,
            self::ACTION_ACCESS_ADD_PHONE,
            self::ACTION_ADD_PHONE,
            self::ACTION_ACCESS_EDIT_PHONE,
            self::ACTION_EDIT_PHONE,
            self::ACTION_ACCESS_USER_SAME_PHONE,
            self::ACTION_ACCESS_ADD_EMAIL,
            self::ACTION_ADD_EMAIL,
            self::ACTION_ACCESS_EDIT_EMAIL,
            self::ACTION_EDIT_EMAIL,
            self::ACTION_ACCESS_USER_SAME_EMAIL,
            self::ACTION_ACCESS_UPDATE_CLIENT,
            self::ACTION_SUBSCRIBE,
            self::ACTION_UNSUBSCRIBE,
            self::ACTION_SHOW_LEADS_BY_IP,
        ],
        self::PHONE_CREATE_FORM => [
            self::ACTION_EDIT,
            self::ACTION_VIEW,
        ],
        self::EMAIL_CREATE_FORM => [
            self::ACTION_EDIT,
            self::ACTION_VIEW,
        ],
        self::CLIENT_CREATE_FORM => [
            self::ACTION_EDIT,
            self::ACTION_VIEW,
        ],
        //self::UI_MENU_CLIENT_INFO => [self::ACTION_ACCESS],
        //self::ACT_CLIENT_SUBSCRIBE => [self::ACTION_ACCESS],
        //self::ACT_CLIENT_UNSUBSCRIBE => [self::ACTION_ACCESS],
        //self::ACT_SEARCH_LEADS_BY_IP => [self::ACTION_ACCESS],
        self::ACT_TAKE_LEAD => [self::ACTION_ACCESS],
        self::LOGIC_CLIENT_DATA  => [self::ACTION_UNMASK],
        //self::UI_FIELD_PHONE_FORM_ADD_PHONE  => [self::ACTION_CREATE, self::ACTION_UPDATE],
        //self::UI_FIELD_EMAIL_FORM_ADD_EMAIL  => [self::ACTION_CREATE, self::ACTION_UPDATE],
        //self::UI_FIELD_LOCALE_FORM_UPDATE_CLIENT  => [self::ACTION_UPDATE],
        //self::UI_FIELD_MARKETING_COUNTRY  => [self::ACTION_UPDATE],
        self::QUERY_SOLD_ALL  => [self::ACTION_ACCESS],
        self::QUERY_SOLD_PROJECTS  => [self::ACTION_ACCESS],
        self::QUERY_SOLD_DEPARTMENTS  => [self::ACTION_ACCESS],
        self::QUERY_SOLD_GROUPS  => [self::ACTION_ACCESS],
        self::QUERY_SOLD_IS_OWNER  => [self::ACTION_ACCESS],
        self::QUERY_SOLD_IS_EMPTY_OWNER  => [self::ACTION_QUERY_AND, self::ACTION_QUERY_OR],
        self::UI_DISPLAY_QUOTE_SEARCH_PARAMS => [self::ACTION_ACCESS],
        self::CMD_AUTO_REDIAL => [self::ACTION_ACCESS],
        self::ACT_CREATE_FROM_PHONE_WIDGET => [self::ACTION_CREATE],
        self::ACT_LINK_TO_CALL => [self::ACTION_ACCESS],
        self::ACT_TAKE_LEAD_FROM_CALL => [self::ACTION_ACCESS],
        self::OBJ_LEAD_PREFERENCES => [self::ACTION_SET_DELAY_CHARGE],
        self::OBJ_LEAD => [self::ACTION_CLONE],
    ];

    public const ATTR_LEAD_IS_OWNER = [
        'optgroup' => 'User',
        'id' => self::NS . 'is_owner',
        'field' => 'is_owner',
        'label' => 'Is Lead`s Owner',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    public const ATTR_LEAD_HAS_OWNER = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'has_owner',
        'field' => 'has_owner',
        'label' => 'Has Owner',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_LEAD_HAS_OWNER_QUERY = [
        'optgroup' => 'Query',
        'id' => self::NS . 'has_owner_query',
        'field' => 'has_owner_query',
        'label' => 'Condition',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['false' => 'Allow', 'true' => 'Dany'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_COMMON_GROUP = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'is_common_group',
        'field' => 'is_common_group',
        'label' => 'Is Common Group',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_EMPLOYEE_SHIFT_TIME = [
        'optgroup' => 'User',
        'id' => self::NS . 'isShiftTime',
        'field' => 'isShiftTime',
        'label' => 'Is Shift Time',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_WITHIN_PERSONAL_TAKE_LIMITS = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'withinPersonalTakeLimits',
        'field' => 'withinPersonalTakeLimits',
        'label' => 'Within Personal Take Limits',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_HAS_APPLIED_QUOTE = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'hasAppliedQuote',
        'field' => 'hasAppliedQuote',
        'label' => 'Has Applied Quote',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_CAN_TAKE_BY_FREQUENCY_MINUTES = [
        'optgroup' => 'User',
        'id' => self::NS . 'canTakeByFrequencyMinutes',
        'field' => 'canTakeByFrequencyMinutes',
        'label' => 'Can Take By Frequency Minutes',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_IN_DEPARTMENT = [
        'optgroup' => 'User',
        'id' => self::NS . 'isInDepartment',
        'field' => 'isInDepartment',
        'label' => 'Has Access to Lead`s Department',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_IN_PROJECT = [
        'optgroup' => 'User',
        'id' => self::NS . 'isInProject',
        'field' => 'isInProject',
        'label' => 'Has Access to Lead`s Project',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    public const ATTR_LEAD_PROJECT_NAME = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'project_name',
        'field' => 'project_name',
        'label' => 'Project',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' => [self::OP_IN],
    ];

    public const ATTR_LEAD_DEPARTMENT_NAME = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'department_name',
        'field' => 'department_name',
        'label' => 'Department',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' => [self::OP_IN],
    ];

    public const ATTR_LEAD_STATUS = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'status_id',
        'field' => 'status_id',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    public const ATTR_LEAD_STATUS_NAME = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'status_name',
        'field' => 'status_name',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2]
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

    public const ATTR_CLIENT_IS_EXCLUDED = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'client_is_excluded',
        'field' => 'client_is_excluded',
        'label' => 'Client is excluded',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    public const ATTR_CLIENT_IS_UNSUBSCRIBE = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'client_is_unsubscribe',
        'field' => 'client_is_unsubscribe',
        'label' => 'Client is unsubscribe',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2]
    ];

    public const OBJECT_ATTRIBUTE_LIST = [
        self::ACT_USER_CONVERSION    => [self::ATTR_LEAD_IS_OWNER],
        self::UI_BLOCK_CLIENT_INFO   => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        /*self::UI_MENU_CLIENT_INFO    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],*/
        /*self::ACT_CLIENT_DETAILS    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],*/
        /*self::ACT_CLIENT_ADD_PHONE    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],*/
        /*self::ACT_CLIENT_ADD_EMAIL    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],*/
        /*self::ACT_CLIENT_UPDATE    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],*/
        /*self::ACT_CLIENT_SUBSCRIBE    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],*/
        /*self::ACT_CLIENT_UNSUBSCRIBE    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],*/
        /*self::ACT_CLIENT_EDIT_PHONE    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],*/
        /*self::ACT_USER_SAME_PHONE_INFO    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],*/
        /*self::ACT_CLIENT_EDIT_EMAIL    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],*/
        /*self::ACT_USER_SAME_EMAIL_INFO    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],*/
        /*self::ACT_SEARCH_LEADS_BY_IP    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],*/

        self::ACT_TAKE_LEAD    => [
            self::ATTR_IS_EMPLOYEE_SHIFT_TIME,
            self::ATTR_IS_IN_PROJECT,
            self::ATTR_IS_IN_DEPARTMENT,
            self::ATTR_HAS_APPLIED_QUOTE,
            self::ATTR_WITHIN_PERSONAL_TAKE_LIMITS,
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_CAN_TAKE_BY_FREQUENCY_MINUTES,
        ],

        self::OBJ_LEAD_PREFERENCES    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],

        self::OBJ_LEAD => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP,
            self::ATTR_IS_EMPLOYEE_SHIFT_TIME,
            self::ATTR_IS_IN_PROJECT,
            self::ATTR_IS_IN_DEPARTMENT,
            self::ATTR_HAS_APPLIED_QUOTE,
            self::ATTR_WITHIN_PERSONAL_TAKE_LIMITS,
            self::ATTR_CAN_TAKE_BY_FREQUENCY_MINUTES,
        ],

        self::LOGIC_CLIENT_DATA  => [self::ATTR_LEAD_IS_OWNER],
        //self::UI_FIELD_PHONE_FORM_ADD_PHONE  => [self::ATTR_LEAD_IS_OWNER],
        self::PHONE_CREATE_FORM  => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_IS_NEW_RECORD
        ],
        self::EMAIL_CREATE_FORM  => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_IS_NEW_RECORD
        ],
        self::CLIENT_CREATE_FORM  => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_IS_NEW_RECORD
        ],
        //self::UI_FIELD_EMAIL_FORM_ADD_EMAIL  => [self::ATTR_LEAD_IS_OWNER],
        self::QUERY_SOLD_IS_EMPTY_OWNER  => [self::ATTR_LEAD_HAS_OWNER_QUERY],
        self::CMD_AUTO_REDIAL  => [],
        self::ACT_TAKE_LEAD_FROM_CALL  => [],
    ];

    /**
     * @return string[]
     */
    public static function getObjectList(): array
    {
        return self::OBJECT_LIST;
    }

    /**
     * @return string[]
     */
    public static function getObjectActionList(): array
    {
        return self::OBJECT_ACTION_LIST;
    }

    /**
     * @return \array[][]
     */
    public static function getObjectAttributeList(): array
    {
        $leadStatuses = Lead::getAllStatuses();
        $attrStatus = self::ATTR_LEAD_STATUS;
        $attrStatus['values'] = $leadStatuses;
        $attrPhoneCreateFieldsList = self::ATTR_FIELD_NAME;
        $attrEmailCreateFieldsList = self::ATTR_FIELD_NAME;
        $attrClientCreateFieldsList = self::ATTR_FIELD_NAME;
        $attrClientCreateMultiFieldsList = self::ATTR_MULTI_FIELD_NAME;

        $formPhoneCreateFields = [
            'phone' => 'Phone'
        ];

        $formEmailCreateFields = [
            'email' => 'Email'
        ];

        $formClientCreateFields = [
            'locale' => 'Locale',
            'marketingCountry' => 'Marketing Country',
        ];

        $attrPhoneCreateFieldsList['values'] = $formPhoneCreateFields;
        $attrEmailCreateFieldsList['values'] = $formEmailCreateFields;
        $attrClientCreateFieldsList['values'] = $formClientCreateFields;
        $attrClientCreateMultiFieldsList['values'] = $formClientCreateFields;

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::UI_BLOCK_CLIENT_INFO][] = $attrStatus;
        //$attributeList[self::UI_MENU_CLIENT_INFO][] = $attrStatus;
        //$attributeList[self::ACT_CLIENT_DETAILS][] = $attrStatus;
        $attributeList[self::ACT_TAKE_LEAD][] = $attrStatus;
        //$attributeList[self::ACT_CLIENT_ADD_PHONE][] = $attrStatus;
        //$attributeList[self::ACT_CLIENT_ADD_EMAIL][] = $attrStatus;
        //$attributeList[self::ACT_CLIENT_UPDATE][] = $attrStatus;
        //$attributeList[self::ACT_CLIENT_SUBSCRIBE][] = $attrStatus;
        //$attributeList[self::ACT_CLIENT_UNSUBSCRIBE][] = $attrStatus;
        //$attributeList[self::ACT_CLIENT_EDIT_PHONE][] = $attrStatus;
        //$attributeList[self::ACT_USER_SAME_PHONE_INFO][] = $attrStatus;
        //$attributeList[self::ACT_CLIENT_EDIT_EMAIL][] = $attrStatus;
        //$attributeList[self::ACT_USER_SAME_EMAIL_INFO][] = $attrStatus;
        //$attributeList[self::ACT_SEARCH_LEADS_BY_IP][] = $attrStatus;
        $attributeList[self::ACT_TAKE_LEAD_FROM_CALL][] = $attrStatus;
        $attributeList[self::OBJ_LEAD_PREFERENCES][] = $attrStatus;
        $attributeList[self::OBJ_LEAD][] = $attrStatus;
        $attributeList[self::PHONE_CREATE_FORM][] = $attrPhoneCreateFieldsList;
        $attributeList[self::EMAIL_CREATE_FORM][] = $attrEmailCreateFieldsList;
        $attributeList[self::CLIENT_CREATE_FORM][] = $attrClientCreateFieldsList;
        $attributeList[self::CLIENT_CREATE_FORM][] = $attrClientCreateMultiFieldsList;

        return $attributeList;
    }
}
