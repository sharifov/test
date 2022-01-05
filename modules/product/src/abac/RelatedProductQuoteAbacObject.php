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
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\entities\productType\ProductTypeQuery;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\CasesStatus;

class RelatedProductQuoteAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'product/product-quote/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** OBJECT PERMISSION */
    public const OBJ_RELATED_PRODUCT_QUOTE = self::NS . 'obj/related-product-quote';

    public const OBJECT_LIST = [
        self::OBJ_RELATED_PRODUCT_QUOTE => self::OBJ_RELATED_PRODUCT_QUOTE
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_READ  = 'read';
    public const ACTION_CREATE  = 'create';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_DELETE  = 'delete';
    public const ACTION_ACCESS_DETAILS = 'accessDetails';
    public const ACTION_SEND_SC_EMAIL = 'sendSCEmail';
    public const ACTION_ACCESS_DIFF = 'accessDifference';
    public const ACTION_SET_CONFIRMED = 'setConfirmed';
    public const ACTION_SET_REFUNDED = 'setRefunded';
    public const ACTION_SET_RECOMMENDED = 'setRecommended';
    public const ACTION_SET_DECLINE = 'setDecline';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [

        self::OBJ_RELATED_PRODUCT_QUOTE => [
            self::ACTION_ACCESS_DETAILS,
            self::ACTION_SEND_SC_EMAIL,
            self::ACTION_ACCESS_DIFF,
            self::ACTION_SET_CONFIRMED,
            self::ACTION_SET_REFUNDED,
            self::ACTION_SET_RECOMMENDED,
            self::ACTION_SET_DECLINE,
        ]
    ];

    protected const ATTR_RELATED_QUOTE_STATUS = [
        'optgroup' => 'RELATED QUOTE',
        'id' => self::NS . 'relPqStatusId',
        'field' => 'relPqStatusId',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_RELATED_QUOTE_OWNER = [
        'optgroup' => 'RELATED QUOTE',
        'id' => self::NS . 'relPqIsOwner',
        'field' => 'relPqIsOwner',
        'label' => 'Is Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_RELATED_QUOTE_HAS_PQR_ACTIVE = [
        'optgroup' => 'RELATED QUOTE',
        'id' => self::NS . 'relPqHasPqrActive',
        'field' => 'relPqHasPqrActive',
        'label' => 'Has Active Refund',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_RELATED_QUOTE_HAS_PQC_ACTIVE = [
        'optgroup' => 'RELATED QUOTE',
        'id' => self::NS . 'relPqHasPqcActive',
        'field' => 'relPqHasPqcActive',
        'label' => 'Has Active Change',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_RELATED_QUOTE_RECOMMENDED = [
        'optgroup' => 'RELATED QUOTE',
        'id' => self::NS . 'relPqIsRecommended',
        'field' => 'relPqIsRecommended',
        'label' => 'Is Recommended',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_RELATED_QUOTE_IS_CHANGEABLE = [
        'optgroup' => 'RELATED QUOTE',
        'id' => self::NS . 'relPqIsChangeable',
        'field' => 'relPqIsChangeable',
        'label' => 'Is Changeable',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_RELATION_TYPE = [
        'optgroup' => 'RELATION',
        'id' => self::NS . 'relationType',
        'field' => 'relationType',
        'label' => 'Type',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_PARENT_QUOTE_STATUS = [
        'optgroup' => 'PARENT QUOTE',
        'id' => self::NS . 'parPqStatusId',
        'field' => 'parPqStatusId',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_PARENT_QUOTE_OWNER = [
        'optgroup' => 'PARENT QUOTE',
        'id' => self::NS . 'parPqIsOwner',
        'field' => 'parPqIsOwner',
        'label' => 'Is Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_PARENT_QUOTE_HAS_PQR_ACTIVE = [
        'optgroup' => 'PARENT QUOTE',
        'id' => self::NS . 'parPqHasPqrActive',
        'field' => 'parPqHasPqrActive',
        'label' => 'Has Active Refund',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_PARENT_QUOTE_HAS_PQC_ACTIVE = [
        'optgroup' => 'PARENT QUOTE',
        'id' => self::NS . 'parPqHasPqcActive',
        'field' => 'parPqHasPqcActive',
        'label' => 'Has Active Change',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_HAS_PQC_INVOLUNTARY_ACTIVE = [
        'optgroup' => 'PARENT QUOTE',
        'id' => self::NS . 'parHasPqcInvoluntaryActive',
        'field' => 'parHasPqcInvoluntaryActive',
        'label' => 'Has Active Involuntary Change',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_PARENT_QUOTE_IS_CHANGEABLE = [
        'optgroup' => 'PARENT QUOTE',
        'id' => self::NS . 'parPqIsChangeable',
        'field' => 'parPqIsChangeable',
        'label' => 'Is Changeable',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_PARENT_PRODUCT_TYPE = [
        'optgroup' => 'PARENT QUOTE PRODUCT',
        'id' => self::NS . 'parPrTypeId',
        'field' => 'parPrTypeId',
        'label' => 'Type',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_PARENT_PRODUCT_PROJECT = [
        'optgroup' => 'PARENT QUOTE PRODUCT',
        'id' => self::NS . 'parPrProjectId',
        'field' => 'parPrProjectId',
        'label' => 'Project',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_ORDER_PROJECT = [
        'optgroup' => 'PARENT QUOTE ORDER',
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
        'optgroup' => 'PARENT QUOTE ORDER',
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
        'optgroup' => 'PARENT QUOTE ORDER',
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
        'optgroup' => 'PARENT QUOTE ORDER',
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
        'optgroup' => 'PARENT QUOTE ORDER',
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

    protected const ATTR_PQC_REFUND_ALLOWED = [
        'optgroup' => 'PRODUCT QUOTE CHANGE',
        'id' => self::NS . 'pqcRefundAllowed',
        'field' => 'pqcRefundAllowed',
        'label' => 'Refund Allowed',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
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

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::OBJ_RELATED_PRODUCT_QUOTE => [
            self::ATTR_RELATED_QUOTE_OWNER,
            self::ATTR_RELATED_QUOTE_HAS_PQR_ACTIVE,
            self::ATTR_RELATED_QUOTE_HAS_PQC_ACTIVE,
            self::ATTR_RELATED_QUOTE_RECOMMENDED,
            self::ATTR_PARENT_QUOTE_OWNER,
            self::ATTR_PARENT_QUOTE_HAS_PQR_ACTIVE,
            self::ATTR_PARENT_QUOTE_HAS_PQC_ACTIVE,
            self::ATTR_ORDER_OWNER,
            self::ATTR_IS_AUTOMATE_PQC,
            self::ATTR_CASE_OWNER,
            self::ATTR_IS_COMMON_GROUP,
            self::ATTR_IS_AUTOMATE_CASE,
            self::ATTR_RELATED_QUOTE_IS_CHANGEABLE,
            self::ATTR_PARENT_QUOTE_IS_CHANGEABLE,
            self::ATTR_HAS_PQC_INVOLUNTARY_ACTIVE
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
        $projects = Project::getList();
        $productQuoteStatuses = ProductQuoteStatus::getList();

        $attrRelQtStatusList = self::ATTR_RELATED_QUOTE_STATUS;
        $attrRelationTypeList = self::ATTR_RELATION_TYPE;
        $attrParQtStatusList = self::ATTR_PARENT_QUOTE_STATUS;
        $attrProductTypeList = self::ATTR_PARENT_PRODUCT_TYPE;
        $attrProductProjectList = self::ATTR_PARENT_PRODUCT_PROJECT;
        $attrOrderProjectList = self::ATTR_ORDER_PROJECT;
        $attrOrderStatusList = self::ATTR_ORDER_STATUS;
        $attrOrderPayStatusList = self::ATTR_ORDER_PAY_STATUS;
        $attrOrderTypeList = self::ATTR_ORDER_TYPE;
        $attrPqcTypeList = self::ATTR_PQC_TYPE;
        $attrPqcStatusList = self::ATTR_PQC_STATUS;
        $attrPqcDecisionList = self::ATTR_PQC_DECISION;
        $attrPqcRefundAllowedList = self::ATTR_PQC_REFUND_ALLOWED;
        $attrCaseCategoryList = self::ATTR_CASE_CATEGORY;
        $attrCaseProjectList = self::ATTR_CASE_PROJECT;
        $attrCaseStatusList = self::ATTR_CASE_STATUS;

        $attrRelQtStatusList['values'] = $productQuoteStatuses;
        $attrRelationTypeList['values'] = ProductQuoteRelation::TYPE_LIST;
        $attrParQtStatusList['values'] = $productQuoteStatuses;
        $attrProductTypeList['values'] = ProductTypeQuery::getListAll();
        $attrProductProjectList['values'] = $projects;
        $attrOrderProjectList['values'] = $projects;
        $attrOrderStatusList['values'] = OrderStatus::getList();
        $attrOrderPayStatusList['values'] = OrderPayStatus::getList();
        $attrOrderTypeList['values'] = OrderSourceType::LIST;
        $attrPqcTypeList['values'] = ProductQuoteChange::TYPE_LIST;
        $attrPqcStatusList['values'] = ProductQuoteChangeStatus::getList();
        $attrPqcDecisionList['values'] = ProductQuoteChangeDecisionType::getList();
        $attrCaseCategoryList['values'] = CaseCategory::getList();
        $attrCaseProjectList['values'] = $projects;
        $attrCaseStatusList['values'] = CasesStatus::STATUS_LIST;

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrRelQtStatusList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrRelationTypeList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrParQtStatusList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrProductTypeList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrProductProjectList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrOrderProjectList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrOrderStatusList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrOrderPayStatusList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrOrderTypeList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrPqcTypeList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrPqcStatusList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrPqcDecisionList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrCaseCategoryList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrCaseProjectList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrCaseStatusList;
        $attributeList[self::OBJ_RELATED_PRODUCT_QUOTE][] = $attrPqcRefundAllowedList;

        return $attributeList;
    }
}
