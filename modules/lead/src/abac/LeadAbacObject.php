<?php

namespace modules\lead\src\abac;

use common\models\Lead;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use src\model\leadStatusReason\entity\LeadStatusReasonQuery;

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
    public const ACT_TAKE_LEAD = self::NS . 'act/take-lead';
    public const ACT_CREATE_FROM_PHONE_WIDGET = self::NS . 'act/create-from-phone-widget';
    public const ACT_LINK_TO_CALL = self::NS . 'act/link-to-call';
    public const ACT_TAKE_LEAD_FROM_CALL = self::NS . 'act/take-from-call';
    public const ACT_PRICE_LINK_RESEARCH = self::NS . 'act/price-link-research';
    public const ACT_ADD_AUTO_QUOTES = self::NS . 'act/auto-add-quotes';

    /** UI PERMISSION */
    public const UI_BLOCK_CLIENT_INFO  = self::NS . 'ui/block/client-info';
    public const PHONE_CREATE_FORM = self::NS . 'form/phone_create';
    public const EMAIL_CREATE_FORM = self::NS . 'form/email_create';
    public const CLIENT_CREATE_FORM = self::NS . 'form/client_create';
    public const UI_DISPLAY_QUOTE_SEARCH_PARAMS = self::NS . 'ui/quote/search/params';
    public const UI_DISPLAY_MARKETING_SOURCE = self::NS . 'ui/block/marketing_source';
    public const CHANGE_SPLIT_TIPS  = self::NS . 'change_split_tips';

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
    public const OBJ_EXTRA_QUEUE         = self::NS . 'obj/extra_queue';
    public const OBJ_CLOSED_QUEUE        = self::NS . 'obj/closed_queue';
    public const OBJ_LEAD_SMART_SEARCH   = self::NS . 'obj/smart_search';
    public const OBJ_LEAD_QUOTE_SEARCH   = self::NS . 'obj/quote_search';
    public const OBJ_LEAD_QUICK_SEARCH   = self::NS . 'obj/quick_search';
    public const OBJ_HEAT_MAP_LEAD       = self::NS . 'obj/heat_map_lead';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::ACT_USER_CONVERSION   => self::ACT_USER_CONVERSION,
        self::ACT_TAKE_LEAD => self::ACT_TAKE_LEAD,
        self::ACT_PRICE_LINK_RESEARCH => self::ACT_PRICE_LINK_RESEARCH,
        self::UI_BLOCK_CLIENT_INFO  => self::UI_BLOCK_CLIENT_INFO,
        self::LOGIC_CLIENT_DATA   => self::LOGIC_CLIENT_DATA,
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
        self::OBJ_EXTRA_QUEUE => self::OBJ_EXTRA_QUEUE,
        self::OBJ_CLOSED_QUEUE => self::OBJ_CLOSED_QUEUE,
        self::OBJ_LEAD_SMART_SEARCH => self::OBJ_LEAD_SMART_SEARCH,
        self::ACT_ADD_AUTO_QUOTES => self::ACT_ADD_AUTO_QUOTES,
        self::OBJ_LEAD_QUOTE_SEARCH => self::OBJ_LEAD_QUOTE_SEARCH,
        self::OBJ_LEAD_QUICK_SEARCH => self::OBJ_LEAD_QUICK_SEARCH,
        self::OBJ_HEAT_MAP_LEAD => self::OBJ_HEAT_MAP_LEAD,
        self::UI_DISPLAY_MARKETING_SOURCE => self::UI_DISPLAY_MARKETING_SOURCE,
        self::CHANGE_SPLIT_TIPS  => self::CHANGE_SPLIT_TIPS,
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
    public const ACTION_SNOOZE = 'snooze';
    public const ACTION_MANAGE_LEAD_PREF_CURRENCY = 'manageLeadPrefCurrency';
    public const ACTION_CLOSE = 'close';
    public const ACTION_TRASH = 'trash';
    public const ACTION_TO_QA_LIST = 'toQaList';
    public const ACTION_ACCESS_SMART_SEARCH = 'accessSmartSearch';
    public const ACTION_ACCESS_QUOTE_SEARCH = 'accessQuoteSearch';
    public const ACTION_ACCESS_QUICK_SEARCH = 'accessQuickSearch';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_USER_CONVERSION  => [self::ACTION_READ, self::ACTION_DELETE, self::ACTION_CREATE],
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
        self::ACT_TAKE_LEAD => [self::ACTION_ACCESS],
        self::ACT_PRICE_LINK_RESEARCH => [self::ACTION_ACCESS],
        self::LOGIC_CLIENT_DATA  => [self::ACTION_UNMASK],
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
        self::OBJ_LEAD_PREFERENCES => [self::ACTION_SET_DELAY_CHARGE, self::ACTION_MANAGE_LEAD_PREF_CURRENCY],
        self::OBJ_LEAD => [self::ACTION_CREATE, self::ACTION_CLONE, self::ACTION_SNOOZE, self::ACTION_CLOSE, self::ACTION_TRASH, self::ACTION_TO_QA_LIST],
        self::OBJ_EXTRA_QUEUE => [self::ACTION_ACCESS],
        self::OBJ_CLOSED_QUEUE => [self::ACTION_ACCESS],
        self::OBJ_LEAD_SMART_SEARCH => [self::ACTION_ACCESS_SMART_SEARCH],
        self::ACT_ADD_AUTO_QUOTES => [self::ACTION_ACCESS],
        self::OBJ_LEAD_QUOTE_SEARCH => [self::ACTION_ACCESS_QUOTE_SEARCH],
        self::OBJ_LEAD_QUICK_SEARCH => [self::ACTION_ACCESS_QUICK_SEARCH],
        self::OBJ_HEAT_MAP_LEAD => [self::ACTION_ACCESS],
        self::UI_DISPLAY_MARKETING_SOURCE => [self::ACTION_READ],
        self::CHANGE_SPLIT_TIPS => [self::ACTION_UPDATE],
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

    public const ATTR_SNOOZE_COUNT = [
        'optgroup' => 'User',
        'id' => self::NS . 'snooze_count',
        'field' => 'snoozeCount',
        'label' => 'Users Snooze Leads count',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_TEXT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '<', '>', '<=', '>=']
    ];

    public const ATTR_QUOTES_COUNT = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'quotesCount',
        'field' => 'quotesCount',
        'label' => 'Quotes Count in Lead',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_TEXT,
        'values' => [],
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '<', '>', '<=', '>=']
    ];

    public const ATTR_FLIGHT_SEGMENT_COUNT = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'flightSegmentsCount',
        'field' => 'flightSegmentsCount',
        'label' => 'Flight Segments Count in Lead',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_TEXT,
        'values' => [],
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '<', '>', '<=', '>=']
    ];

    public const ATTR_CLOSE_REASON = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'close_reason',
        'field' => 'closeReason',
        'label' => 'Lead Close Reason Key',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_CONTAINS]
    ];

    public const OBJECT_ATTRIBUTE_LIST = [
        self::ACT_USER_CONVERSION    => [self::ATTR_LEAD_IS_OWNER],
        self::UI_BLOCK_CLIENT_INFO   => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],

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
        self::ACT_PRICE_LINK_RESEARCH => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_FLIGHT_SEGMENT_COUNT
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
            self::ATTR_SNOOZE_COUNT
        ],

        self::LOGIC_CLIENT_DATA  => [self::ATTR_LEAD_IS_OWNER],
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
        self::QUERY_SOLD_IS_EMPTY_OWNER  => [self::ATTR_LEAD_HAS_OWNER_QUERY],
        self::CMD_AUTO_REDIAL  => [],
        self::ACT_TAKE_LEAD_FROM_CALL  => [],
        self::OBJ_LEAD_SMART_SEARCH => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP,
            self::ATTR_IS_IN_PROJECT,
            self::ATTR_IS_IN_DEPARTMENT,
        ],
        self::ACT_ADD_AUTO_QUOTES  => [],
        self::OBJ_LEAD_QUOTE_SEARCH => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP,
            self::ATTR_IS_IN_PROJECT,
            self::ATTR_IS_IN_DEPARTMENT,
        ],
        self::OBJ_LEAD_QUICK_SEARCH => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP,
            self::ATTR_IS_IN_PROJECT,
            self::ATTR_IS_IN_DEPARTMENT,
        ],
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
        $attrLeadCloseReasons = self::ATTR_CLOSE_REASON;

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

        $closeReasons = LeadStatusReasonQuery::getList();

        $attrPhoneCreateFieldsList['values'] = $formPhoneCreateFields;
        $attrEmailCreateFieldsList['values'] = $formEmailCreateFields;
        $attrClientCreateFieldsList['values'] = $formClientCreateFields;
        $attrClientCreateMultiFieldsList['values'] = $formClientCreateFields;
        $attrLeadCloseReasons['values'] = $closeReasons;

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::UI_BLOCK_CLIENT_INFO][] = $attrStatus;
        $attributeList[self::ACT_TAKE_LEAD][] = $attrStatus;
        $attributeList[self::ACT_TAKE_LEAD_FROM_CALL][] = $attrStatus;
        $attributeList[self::ACT_PRICE_LINK_RESEARCH][] = $attrStatus;
        $attributeList[self::OBJ_LEAD_PREFERENCES][] = $attrStatus;
        $attributeList[self::OBJ_LEAD][] = $attrStatus;
        $attributeList[self::OBJ_LEAD][] = $attrLeadCloseReasons;
        $attributeList[self::PHONE_CREATE_FORM][] = $attrPhoneCreateFieldsList;
        $attributeList[self::EMAIL_CREATE_FORM][] = $attrEmailCreateFieldsList;
        $attributeList[self::CLIENT_CREATE_FORM][] = $attrClientCreateFieldsList;
        $attributeList[self::CLIENT_CREATE_FORM][] = $attrClientCreateMultiFieldsList;
        $attributeList[self::ACT_USER_CONVERSION][] = $attrLeadCloseReasons;
        $attributeList[self::OBJ_LEAD_SMART_SEARCH][] = $attrStatus;
        $attributeList[self::OBJ_LEAD_QUOTE_SEARCH][] = $attrStatus;
        $attributeList[self::OBJ_LEAD_QUICK_SEARCH][] = $attrStatus;
        $attributeList[self::ACT_ADD_AUTO_QUOTES][] = self::ATTR_QUOTES_COUNT;
        $attributeList[self::ACT_ADD_AUTO_QUOTES][] = self::ATTR_FLIGHT_SEGMENT_COUNT;

        return $attributeList;
    }
}
