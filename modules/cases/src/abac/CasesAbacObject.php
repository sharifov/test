<?php

namespace modules\cases\src\abac;

use common\models\Department;
use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use src\entities\cases\CaseCategory;
use src\entities\cases\CasesStatus;

class CasesAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    public const NS = 'case/case/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** LOGIC PERMISSION */
    public const LOGIC_CLIENT_DATA   = self::NS . 'logic/client_data';

    /** QUERY PERMISSIONS */
    public const SQL_CASE_QUEUES = self::NS . 'sql/queue';

    /** UI PERMISSION */
    public const UI_BLOCK_EVENT_LOG_LIST  = self::NS . 'ui/block/event-log-list';
    public const UI_BTN_EVENT_LOG_VIEW    = self::NS . 'ui/btn/event-log-view';

    /** OBJECT PERMISSION */
    public const OBJ_CASE_STATUS_ROUTE_RULES = self::NS . 'obj/status_rules';

    public const OBJECT_LIST = [
        self::LOGIC_CLIENT_DATA             => self::LOGIC_CLIENT_DATA,
        self::UI_BLOCK_EVENT_LOG_LIST       => self::UI_BLOCK_EVENT_LOG_LIST,
        self::UI_BTN_EVENT_LOG_VIEW         => self::UI_BTN_EVENT_LOG_VIEW,
        self::OBJ_CASE_STATUS_ROUTE_RULES   => self::OBJ_CASE_STATUS_ROUTE_RULES,
        self::SQL_CASE_QUEUES               => self::SQL_CASE_QUEUES,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_UNMASK  = 'unmask';
    public const ACTION_ACCESS  = 'access';
    public const ACTION_ALL_ACCESS  = 'allAccess';
    public const ACTION_OWNER_ACCESS  = 'ownerAccess';
    public const ACTION_EMPTY_OWNER_ACCESS  = 'emptyOwnerAccess';
    public const ACTION_GROUP_ACCESS  = 'groupAccess';
    //public const ACTION_DEPARTMENT_ACCESS  = 'departmentAccess';
    //public const ACTION_PROJECT_ACCESS  = 'projectAccess';
    public const ACTION_READ  = 'read';
    public const ACTION_CREATE  = 'create';
    public const ACTION_TRANSFER  = 'transfer';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::LOGIC_CLIENT_DATA                 => [self::ACTION_UNMASK],
        self::UI_BLOCK_EVENT_LOG_LIST           => [self::ACTION_ACCESS],
        self::UI_BTN_EVENT_LOG_VIEW             => [self::ACTION_READ],
        self::OBJ_CASE_STATUS_ROUTE_RULES   => [self::ACTION_TRANSFER],
        self::SQL_CASE_QUEUES => [
            self::ACTION_OWNER_ACCESS,
            self::ACTION_EMPTY_OWNER_ACCESS,
            self::ACTION_GROUP_ACCESS,
            self::ACTION_ALL_ACCESS,
            //self::ACTION_DEPARTMENT_ACCESS,
            //self::ACTION_PROJECT_ACCESS,
        ],
    ];

    public const ATTR_CASE_IS_OWNER = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'is_owner',
        'field' => 'is_owner',
        'label' => 'Is Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        //'validation' => ['allow_empty_value' => true],
        'operators' =>  [self::OP_EQUAL2]
    ];

    public const ATTR_CASE_HAS_OWNER = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'has_owner',
        'field' => 'has_owner',
        'label' => 'Has Owner',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    public const ATTR_CASE_PROJECT_NAME = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'project_name',
        'field' => 'project_name',
        'label' => 'Project',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' => [self::OP_IN],
    ];

    public const ATTR_CASE_DEPARTMENT_NAME = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'department_name',
        'field' => 'department_name',
        'label' => 'Department',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' => [self::OP_IN],
    ];

    public const ATTR_CASE_STATUS = [
        'optgroup' => 'CASE',
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

    public const ATTR_CASE_STATUS_NAME = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'status_name',
        'field' => 'status_name',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2]
    ];

    protected const ATTR_CASE_STATUS_RULE = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'to_status',
        'field' => 'to_status',
        'label' => 'To Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_CASE_CATEGORY = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'category_id',
        'field' => 'category_id',
        'label' => 'Category',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_IS_COMMON_GROUP = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'is_common_group',
        'field' => 'is_common_group',
        'label' => 'Is Common Group',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_PQC_STATUS = [
        'optgroup' => 'PQ Change',
        'id' => self::NS . 'pqc_status',
        'field' => 'pqc_status',
        'label' => 'PQC Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_PQR_STATUS = [
        'optgroup' => 'PQ Refund',
        'id' => self::NS . 'pqr_status',
        'field' => 'pqr_status',
        'label' => 'PQR Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_MAIN_MENU_CASES_BADGE = [
        'optgroup' => 'MAIN MENU CASES',
        'id' => self::NS . 'mainMenuCaseBadgeName',
        'field' => 'mainMenuCaseBadgeName',
        'label' => 'Queue Badge ID',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    public const ATTR_CLIENT_IS_EXCLUDED = [
        'optgroup' => 'CASE',
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
        'optgroup' => 'CASE',
        'id' => self::NS . 'client_is_unsubscribe',
        'field' => 'client_is_unsubscribe',
        'label' => 'Client is unsubscribe',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2]
    ];

    protected const ATTR_PHONE_FROM_PERSONAL = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'phone_from_personal',
        'field' => 'phone_from_personal',
        'label' => 'Phone From Personal',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_PHONE_FROM_GENERAL = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'phone_from_general',
        'field' => 'phone_from_general',
        'label' => 'Phone From General',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::LOGIC_CLIENT_DATA    => [self::ATTR_CASE_IS_OWNER, self::ATTR_IS_COMMON_GROUP],
        self::OBJ_CASE_STATUS_ROUTE_RULES => [
            self::ATTR_CASE_IS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::SQL_CASE_QUEUES => [],
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
        $attrStatusList = self::ATTR_CASE_STATUS;
        $attrCategoryList = self::ATTR_CASE_CATEGORY;
        $attrStatusRuleList = self::ATTR_CASE_STATUS_RULE;
        $attrPqcStatusList = self::ATTR_PQC_STATUS;
        $attrPqrStatusList = self::ATTR_PQR_STATUS;
        $attrCasesBadge = self::ATTR_MAIN_MENU_CASES_BADGE;

        $attrStatusList['values'] = CasesStatus::STATUS_LIST;
        $attrStatusRuleList['values'] = CasesStatus::STATUS_LIST;
        $attrCategoryList['values'] = CaseCategory::getList();
        $attrPqcStatusList['values'] = ProductQuoteChangeStatus::getList();
        $attrPqrStatusList['values'] = ProductQuoteRefundStatus::getList();
        $attrCasesBadge['values'] = [
            'need_action' => 'Need Action',
            'pending' => 'Pending',
            'inbox' => 'Inbox',
            'unidentified' => 'Unidentified',
            'first-priority' => 'First Priority',
            'second-priority' => 'Second Priority',
            'pass-departure' => 'Pass Departure',
            'processing' => 'Processing',
            'follow-up' => 'FollowUp',
            'awaiting' => 'Awaiting',
            'auto-processing' => 'Auto Processing',
            'solved' => 'Solved',
            'error' => 'Error',
            'trash' => 'Trash',
        ];

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        $attributeList[self::OBJ_CASE_STATUS_ROUTE_RULES][] = $attrStatusList;
        $attributeList[self::OBJ_CASE_STATUS_ROUTE_RULES][] = $attrCategoryList;
        $attributeList[self::OBJ_CASE_STATUS_ROUTE_RULES][] = $attrStatusRuleList;
        $attributeList[self::OBJ_CASE_STATUS_ROUTE_RULES][] = $attrPqcStatusList;
        $attributeList[self::OBJ_CASE_STATUS_ROUTE_RULES][] = $attrPqrStatusList;
        $attributeList[self::SQL_CASE_QUEUES][] = $attrCasesBadge;

        return $attributeList;
    }
}
