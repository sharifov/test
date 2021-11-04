<?php

namespace modules\product\src\abac;

use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\order\OrderStatus;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productType\ProductTypeQuery;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\CasesStatus;

class ProductQuoteChangeAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'product-quote-change/product-quote-change/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_FLIGHT_REPROTECTION_QUOTE  = self::NS . 'act/flight-reprotection-quote';
    public const ACT_FLIGHT_VOLUNTARY_QUOTE  = self::NS . 'act/flight-voluntary-quote';

    public const OBJECT_LIST = [
        self::ACT_FLIGHT_REPROTECTION_QUOTE => self::ACT_FLIGHT_REPROTECTION_QUOTE,
        self::ACT_FLIGHT_VOLUNTARY_QUOTE => self::ACT_FLIGHT_VOLUNTARY_QUOTE
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_READ  = 'read';
    public const ACTION_CREATE  = 'create';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_DELETE  = 'delete';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_FLIGHT_REPROTECTION_QUOTE => [self::ACTION_CREATE],
        self::ACT_FLIGHT_VOLUNTARY_QUOTE => [self::ACTION_CREATE],
    ];

    protected const ATTR_PQC_TYPE = [
        'optgroup' => 'PRODUCT QUOTE CHANGE',
        'id' => self::NS . 'pqcTypeId',
        'field' => 'pqcTypeId',
        'label' => 'Type',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_PQC_STATUS = [
        'optgroup' => 'PRODUCT QUOTE CHANGE',
        'id' => self::NS . 'pqcStatusId',
        'field' => 'pqcStatusId',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_IS_AUTOMATE_PQC = [
        'optgroup' => 'PRODUCT QUOTE CHANGE',
        'id' => self::NS . 'isAutomatePqc',
        'field' => 'isAutomatePqc',
        'label' => 'Is Automate',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_PQC_DECISION = [
        'optgroup' => 'PRODUCT QUOTE CHANGE',
        'id' => self::NS . 'pqcDecisionId',
        'field' => 'pqcDecisionId',
        'label' => 'Decision',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_CASE_CATEGORY = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'csCategoryId',
        'field' => 'csCategoryId',
        'label' => 'Category',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_CASE_OWNER = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'isCaseOwner',
        'field' => 'isCaseOwner',
        'label' => 'Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_COMMON_GROUP = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'isCommonGroup',
        'field' => 'isCommonGroup',
        'label' => 'Is Common Group',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_AUTOMATE_CASE = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'isAutomateCase',
        'field' => 'isAutomateCase',
        'label' => 'Is Automate',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_CASE_PROJECT = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'csProjectId',
        'field' => 'csProjectId',
        'label' => 'Project',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_CASE_STATUS = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'csStatusId',
        'field' => 'csStatusId',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_PRODUCT_QUOTE_STATUS = [
        'optgroup' => 'PRODUCT QUOTE',
        'id' => self::NS . 'pqStatusId',
        'field' => 'pqStatusId',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_PRODUCT_QUOTE_OWNER = [
        'optgroup' => 'PRODUCT QUOTE',
        'id' => self::NS . 'isOwner',
        'field' => 'isOwner',
        'label' => 'Is Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_HAS_PQR_ACTIVE = [
        'optgroup' => 'PRODUCT QUOTE',
        'id' => self::NS . 'hasPqrActive',
        'field' => 'hasPqrActive',
        'label' => 'Has Active Refund',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_HAS_PQC_ACTIVE = [
        'optgroup' => 'PRODUCT QUOTE',
        'id' => self::NS . 'hasPqcActive',
        'field' => 'hasPqcActive',
        'label' => 'Has Active Change',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_PQ_CHANGEABLE = [
        'optgroup' => 'PRODUCT QUOTE',
        'id' => self::NS . 'isPqChangeable',
        'field' => 'isPqChangeable',
        'label' => 'Is Changeable',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_PRODUCT_TYPE = [
        'optgroup' => 'PRODUCT',
        'id' => self::NS . 'prTypeId',
        'field' => 'prTypeId',
        'label' => 'Type',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_PRODUCT_PROJECT = [
        'optgroup' => 'PRODUCT',
        'id' => self::NS . 'prProjectId',
        'field' => 'prProjectId',
        'label' => 'Project',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_ORDER_PROJECT = [
        'optgroup' => 'ORDER',
        'id' => self::NS . 'orProjectId',
        'field' => 'orProjectId',
        'label' => 'Project',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_ORDER_STATUS = [
        'optgroup' => 'ORDER',
        'id' => self::NS . 'orStatusId',
        'field' => 'orStatusId',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_ORDER_PAY_STATUS = [
        'optgroup' => 'ORDER',
        'id' => self::NS . 'orPayStatusId',
        'field' => 'orPayStatusId',
        'label' => 'Pay Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_ORDER_OWNER = [
        'optgroup' => 'ORDER',
        'id' => self::NS . 'isOrderOwner',
        'field' => 'isOrderOwner',
        'label' => 'Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_ORDER_TYPE = [
        'optgroup' => 'ORDER',
        'id' => self::NS . 'orTypeId',
        'field' => 'orTypeId',
        'label' => 'Source Type',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    public const OBJECT_ATTRIBUTE_LIST = [
        self::ACT_FLIGHT_REPROTECTION_QUOTE    => [
            self::ATTR_IS_AUTOMATE_PQC,
            self::ATTR_CASE_OWNER,
            self::ATTR_IS_COMMON_GROUP,
            self::ATTR_IS_AUTOMATE_CASE,
            self::ATTR_PRODUCT_QUOTE_OWNER,
            self::ATTR_IS_PQ_CHANGEABLE,
            self::ATTR_HAS_PQR_ACTIVE,
            self::ATTR_HAS_PQC_ACTIVE,
            self::ATTR_ORDER_OWNER,
        ],
        self::ACT_FLIGHT_VOLUNTARY_QUOTE      => [
            self::ATTR_IS_AUTOMATE_PQC,
            self::ATTR_CASE_OWNER,
            self::ATTR_IS_COMMON_GROUP,
            self::ATTR_IS_AUTOMATE_CASE,
            self::ATTR_PRODUCT_QUOTE_OWNER,
            self::ATTR_IS_PQ_CHANGEABLE,
            self::ATTR_HAS_PQR_ACTIVE,
            self::ATTR_HAS_PQC_ACTIVE,
            self::ATTR_ORDER_OWNER,
        ],
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
        $projects = Project::getList();

        $attrTypeList = self::ATTR_PQC_TYPE;
        $attrStatusList = self::ATTR_PQC_STATUS;
        $attrDecisionList = self::ATTR_PQC_DECISION;
        $attrCaseCategoryList = self::ATTR_CASE_CATEGORY;
        $attrCaseProjectList = self::ATTR_CASE_PROJECT;
        $attrCaseStatusList = self::ATTR_CASE_STATUS;
        $attrPqStatusList = self::ATTR_PRODUCT_QUOTE_STATUS;
        $attrProductTypeList = self::ATTR_PRODUCT_TYPE;
        $attrProductProjectList = self::ATTR_PRODUCT_PROJECT;
        $attrOrderProjectList = self::ATTR_ORDER_PROJECT;
        $attrOrderStatusList = self::ATTR_ORDER_STATUS;
        $attrOrderPayStatusList = self::ATTR_ORDER_PAY_STATUS;
        $attrOrderTypeList = self::ATTR_ORDER_TYPE;

        $attrTypeList['values'] = ProductQuoteChange::TYPE_LIST;
        $attrStatusList['values'] = ProductQuoteChangeStatus::getList();
        $attrDecisionList['values'] = ProductQuoteChangeDecisionType::getList();
        $attrCaseCategoryList['values'] = CaseCategory::getList();
        $attrCaseProjectList['values'] = $projects;
        $attrCaseStatusList['values'] = CasesStatus::STATUS_LIST;
        $attrPqStatusList['values'] = ProductQuoteStatus::getList();
        $attrProductTypeList['values'] = ProductTypeQuery::getListAll();
        $attrProductProjectList['values'] = $projects;
        $attrOrderProjectList['values'] = $projects;
        $attrOrderStatusList['values'] = OrderStatus::getList();
        $attrOrderPayStatusList['values'] = OrderPayStatus::getList();
        $attrOrderTypeList['values'] = OrderSourceType::LIST;

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        $attributeList[self::ACT_FLIGHT_REPROTECTION_QUOTE][] = $attrTypeList;
        $attributeList[self::ACT_FLIGHT_REPROTECTION_QUOTE][] = $attrStatusList;
        $attributeList[self::ACT_FLIGHT_REPROTECTION_QUOTE][] = $attrDecisionList;
        $attributeList[self::ACT_FLIGHT_REPROTECTION_QUOTE][] = $attrCaseCategoryList;
        $attributeList[self::ACT_FLIGHT_REPROTECTION_QUOTE][] = $attrCaseProjectList;
        $attributeList[self::ACT_FLIGHT_REPROTECTION_QUOTE][] = $attrCaseStatusList;
        $attributeList[self::ACT_FLIGHT_REPROTECTION_QUOTE][] = $attrPqStatusList;
        $attributeList[self::ACT_FLIGHT_REPROTECTION_QUOTE][] = $attrProductTypeList;
        $attributeList[self::ACT_FLIGHT_REPROTECTION_QUOTE][] = $attrProductProjectList;
        $attributeList[self::ACT_FLIGHT_REPROTECTION_QUOTE][] = $attrOrderProjectList;
        $attributeList[self::ACT_FLIGHT_REPROTECTION_QUOTE][] = $attrOrderStatusList;
        $attributeList[self::ACT_FLIGHT_REPROTECTION_QUOTE][] = $attrOrderPayStatusList;
        $attributeList[self::ACT_FLIGHT_REPROTECTION_QUOTE][] = $attrOrderTypeList;

        $attributeList[self::ACT_FLIGHT_VOLUNTARY_QUOTE][] = $attrTypeList;
        $attributeList[self::ACT_FLIGHT_VOLUNTARY_QUOTE][] = $attrStatusList;
        $attributeList[self::ACT_FLIGHT_VOLUNTARY_QUOTE][] = $attrDecisionList;
        $attributeList[self::ACT_FLIGHT_VOLUNTARY_QUOTE][] = $attrCaseCategoryList;
        $attributeList[self::ACT_FLIGHT_VOLUNTARY_QUOTE][] = $attrCaseProjectList;
        $attributeList[self::ACT_FLIGHT_VOLUNTARY_QUOTE][] = $attrCaseStatusList;
        $attributeList[self::ACT_FLIGHT_VOLUNTARY_QUOTE][] = $attrPqStatusList;
        $attributeList[self::ACT_FLIGHT_VOLUNTARY_QUOTE][] = $attrProductTypeList;
        $attributeList[self::ACT_FLIGHT_VOLUNTARY_QUOTE][] = $attrProductProjectList;
        $attributeList[self::ACT_FLIGHT_VOLUNTARY_QUOTE][] = $attrOrderProjectList;
        $attributeList[self::ACT_FLIGHT_VOLUNTARY_QUOTE][] = $attrOrderStatusList;
        $attributeList[self::ACT_FLIGHT_VOLUNTARY_QUOTE][] = $attrOrderPayStatusList;
        $attributeList[self::ACT_FLIGHT_VOLUNTARY_QUOTE][] = $attrOrderTypeList;

        return $attributeList;
    }
}
