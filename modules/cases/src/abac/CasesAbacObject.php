<?php

namespace modules\cases\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\CasesStatus;

class CasesAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'case/case/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** LOGIC PERMISSION */
    public const LOGIC_CLIENT_DATA   = self::NS . 'logic/client_data';

    /** ACTION PERMISSION */
    public const ACT_FLIGHT_REPROTECTION_CONFIRM  = self::NS . 'act/flight-reprotection-confirm';
    public const ACT_FLIGHT_REPROTECTION_REFUND  = self::NS . 'act/flight-reprotection-refund';
    public const ACT_FLIGHT_REPROTECTION_QUOTE  = self::NS . 'act/flight-reprotection-quote';
    public const ACT_PRODUCT_QUOTE_REMOVE = self::NS . 'act/product_quote/remove';
    public const ACT_PRODUCT_QUOTE_VIEW_DETAILS = self::NS . 'act/product_quote/view_details';
    public const ACT_REPROTECTION_QUOTE_SEND_EMAIL = self::NS . 'act/reprotection_quote/send_email';
    public const ACT_VIEW_QUOTES_DIFF = self::NS . 'act/reprotection_quote/original_quote_diff';
    public const ACT_VIEW_SET_RECOMMENDED_REPROTECTION_QUOTE = self::NS . 'act/reprotection_quote/set_recommended';

    /** UI PERMISSION */
    public const UI_BLOCK_EVENT_LOG_LIST  = self::NS . 'ui/block/event-log-list';
    public const UI_BTN_EVENT_LOG_VIEW    = self::NS . 'ui/btn/event-log-view';

    /** OBJECT PERMISSION */
    public const OBJ_CASE_STATUS_ROUTE_RULES = self::NS . 'obj/status_rules';

    public const OBJECT_LIST = [
        self::LOGIC_CLIENT_DATA                             => self::LOGIC_CLIENT_DATA,
        self::UI_BLOCK_EVENT_LOG_LIST                       => self::UI_BLOCK_EVENT_LOG_LIST,
        self::UI_BTN_EVENT_LOG_VIEW                         => self::UI_BTN_EVENT_LOG_VIEW,
        self::ACT_REPROTECTION_QUOTE_SEND_EMAIL             => self::ACT_REPROTECTION_QUOTE_SEND_EMAIL,
        self::ACT_PRODUCT_QUOTE_REMOVE                      => self::ACT_PRODUCT_QUOTE_REMOVE,
        self::ACT_PRODUCT_QUOTE_VIEW_DETAILS                => self::ACT_PRODUCT_QUOTE_VIEW_DETAILS,
        self::ACT_FLIGHT_REPROTECTION_CONFIRM               => self::ACT_FLIGHT_REPROTECTION_CONFIRM,
        self::ACT_FLIGHT_REPROTECTION_REFUND                => self::ACT_FLIGHT_REPROTECTION_REFUND,
        self::ACT_FLIGHT_REPROTECTION_QUOTE                 => self::ACT_FLIGHT_REPROTECTION_QUOTE,
        self::ACT_VIEW_QUOTES_DIFF                          => self::ACT_VIEW_QUOTES_DIFF,
        self::ACT_VIEW_SET_RECOMMENDED_REPROTECTION_QUOTE   => self::ACT_VIEW_SET_RECOMMENDED_REPROTECTION_QUOTE,

        self::OBJ_CASE_STATUS_ROUTE_RULES                   => self::OBJ_CASE_STATUS_ROUTE_RULES,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_MASK    = 'mask';
    public const ACTION_UNMASK  = 'unmask';
    public const ACTION_ACCESS  = 'access';
    public const ACTION_READ  = 'read';
    public const ACTION_CREATE  = 'create';
    public const ACTION_TRANSFER  = 'transfer';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::LOGIC_CLIENT_DATA                 => [self::ACTION_UNMASK],
        self::UI_BLOCK_EVENT_LOG_LIST           => [self::ACTION_ACCESS],
        self::UI_BTN_EVENT_LOG_VIEW             => [self::ACTION_READ],
        self::ACT_REPROTECTION_QUOTE_SEND_EMAIL => [self::ACTION_ACCESS],
        self::ACT_PRODUCT_QUOTE_REMOVE          => [self::ACTION_ACCESS],
        self::ACT_PRODUCT_QUOTE_VIEW_DETAILS    => [self::ACTION_ACCESS],
        self::ACT_FLIGHT_REPROTECTION_CONFIRM   => [self::ACTION_ACCESS],
        self::ACT_FLIGHT_REPROTECTION_REFUND    => [self::ACTION_ACCESS],
        self::ACT_FLIGHT_REPROTECTION_QUOTE     => [self::ACTION_CREATE],
        self::ACT_VIEW_QUOTES_DIFF              => [self::ACTION_ACCESS],
        self::ACT_VIEW_SET_RECOMMENDED_REPROTECTION_QUOTE => [self::ACTION_ACCESS],

        self::OBJ_CASE_STATUS_ROUTE_RULES   => [self::ACTION_TRANSFER],
    ];

    protected const ATTR_CASE_IS_OWNER = [
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

    protected const ATTR_CASE_STATUS = [
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

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::LOGIC_CLIENT_DATA    => [self::ATTR_CASE_IS_OWNER, self::ATTR_IS_COMMON_GROUP],
        self::ACT_REPROTECTION_QUOTE_SEND_EMAIL => [
            self::ATTR_CASE_IS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::ACT_FLIGHT_REPROTECTION_CONFIRM => [self::ATTR_CASE_IS_OWNER, self::ATTR_IS_COMMON_GROUP],
        self::ACT_FLIGHT_REPROTECTION_REFUND => [self::ATTR_CASE_IS_OWNER, self::ATTR_IS_COMMON_GROUP],
        self::ACT_FLIGHT_REPROTECTION_QUOTE => [self::ATTR_CASE_IS_OWNER, self::ATTR_IS_COMMON_GROUP],

        self::OBJ_CASE_STATUS_ROUTE_RULES => [
            self::ATTR_CASE_IS_OWNER,
            self::ATTR_IS_COMMON_GROUP
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
        $attrStatusList = self::ATTR_CASE_STATUS;
        $attrCategoryList = self::ATTR_CASE_CATEGORY;
        $attrStatusRuleList = self::ATTR_CASE_STATUS_RULE;
        $attrPqcStatusList = self::ATTR_PQC_STATUS;
        $attrPqrStatusList = self::ATTR_PQR_STATUS;

        $attrStatusList['values'] = CasesStatus::STATUS_LIST;
        $attrStatusRuleList['values'] = CasesStatus::STATUS_LIST;
        $attrCategoryList['values'] = CaseCategory::getList();
        $attrPqcStatusList['values'] = ProductQuoteChangeStatus::getList();
        $attrPqrStatusList['values'] = ProductQuoteRefundStatus::getList();

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        $attributeList[self::OBJ_CASE_STATUS_ROUTE_RULES][] = $attrStatusList;
        $attributeList[self::OBJ_CASE_STATUS_ROUTE_RULES][] = $attrCategoryList;
        $attributeList[self::OBJ_CASE_STATUS_ROUTE_RULES][] = $attrStatusRuleList;

        $attributeList[self::ACT_REPROTECTION_QUOTE_SEND_EMAIL][] = $attrStatusList;
        $attributeList[self::ACT_REPROTECTION_QUOTE_SEND_EMAIL][] = $attrCategoryList;
        $attributeList[self::ACT_REPROTECTION_QUOTE_SEND_EMAIL][] = $attrPqcStatusList;
        $attributeList[self::OBJ_CASE_STATUS_ROUTE_RULES][] = $attrPqcStatusList;
        $attributeList[self::OBJ_CASE_STATUS_ROUTE_RULES][] = $attrPqrStatusList;

        return $attributeList;
    }
}
