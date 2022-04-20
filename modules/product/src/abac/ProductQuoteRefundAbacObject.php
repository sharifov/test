<?php

namespace modules\product\src\abac;

use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\order\OrderStatus;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use modules\product\src\entities\productType\ProductTypeQuery;
use src\entities\cases\CaseCategory;
use src\entities\cases\CasesStatus;

class ProductQuoteRefundAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'product/product-quote-refund/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** OBJECT PERMISSION */
    public const OBJ_PRODUCT_QUOTE_REFUND = self::NS . 'obj/product-quote-refund';

    public const OBJECT_LIST = [
        self::OBJ_PRODUCT_QUOTE_REFUND => self::OBJ_PRODUCT_QUOTE_REFUND
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS_DETAILS  = 'accessDetails';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_SEND_VOL_REFUND_EMAIL = 'sendVoluntaryRefundEmail';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::OBJ_PRODUCT_QUOTE_REFUND => [
            self::ACTION_ACCESS_DETAILS,
            self::ACTION_UPDATE,
            self::ACTION_SEND_VOL_REFUND_EMAIL
        ],
    ];

    protected const ATTR_PQR_TYPE = [
        'optgroup' => 'PRODUCT QUOTE REFUND',
        'id' => self::NS . 'pqrTypeId',
        'field' => 'pqrTypeId',
        'label' => 'Type',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_PQR_STATUS = [
        'optgroup' => 'PRODUCT QUOTE REFUND',
        'id' => self::NS . 'pqrStatusId',
        'field' => 'pqrStatusId',
        'label' => 'Status',
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

    protected const ATTR_HAS_PQC_INVOLUNTARY_ACTIVE = [
        'optgroup' => 'PRODUCT QUOTE',
        'id' => self::NS . 'hasPqcInvoluntaryActive',
        'field' => 'hasPqcInvoluntaryActive',
        'label' => 'Has Active Involuntary Change',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_HAS_PQC_ACCEPTED = [
        'optgroup' => 'PRODUCT QUOTE',
        'id' => self::NS . 'hasPqcAccepted',
        'field' => 'hasPqcAccepted',
        'label' => 'Has accepted change quote',
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
        self::OBJ_PRODUCT_QUOTE_REFUND      => [
            self::ATTR_CASE_OWNER,
            self::ATTR_IS_COMMON_GROUP,
            self::ATTR_IS_AUTOMATE_CASE,
            self::ATTR_PRODUCT_QUOTE_OWNER,
            self::ATTR_IS_PQ_CHANGEABLE,
            self::ATTR_HAS_PQR_ACTIVE,
            self::ATTR_HAS_PQC_ACTIVE,
            self::ATTR_ORDER_OWNER,
            self::ATTR_HAS_PQC_INVOLUNTARY_ACTIVE,
            self::ATTR_HAS_PQC_ACCEPTED,
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

        $attrTypeList = self::ATTR_PQR_TYPE;
        $attrStatusList = self::ATTR_PQR_STATUS;
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

        $attrTypeList['values'] = ProductQuoteRefund::getTypeList();
        $attrStatusList['values'] = ProductQuoteRefundStatus::getList();
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

        $attributeList[self::OBJ_PRODUCT_QUOTE_REFUND][] = $attrTypeList;
        $attributeList[self::OBJ_PRODUCT_QUOTE_REFUND][] = $attrStatusList;
        $attributeList[self::OBJ_PRODUCT_QUOTE_REFUND][] = $attrCaseCategoryList;
        $attributeList[self::OBJ_PRODUCT_QUOTE_REFUND][] = $attrCaseProjectList;
        $attributeList[self::OBJ_PRODUCT_QUOTE_REFUND][] = $attrCaseStatusList;
        $attributeList[self::OBJ_PRODUCT_QUOTE_REFUND][] = $attrPqStatusList;
        $attributeList[self::OBJ_PRODUCT_QUOTE_REFUND][] = $attrProductTypeList;
        $attributeList[self::OBJ_PRODUCT_QUOTE_REFUND][] = $attrProductProjectList;
        $attributeList[self::OBJ_PRODUCT_QUOTE_REFUND][] = $attrOrderProjectList;
        $attributeList[self::OBJ_PRODUCT_QUOTE_REFUND][] = $attrOrderStatusList;
        $attributeList[self::OBJ_PRODUCT_QUOTE_REFUND][] = $attrOrderPayStatusList;
        $attributeList[self::OBJ_PRODUCT_QUOTE_REFUND][] = $attrOrderTypeList;

        return $attributeList;
    }
}
