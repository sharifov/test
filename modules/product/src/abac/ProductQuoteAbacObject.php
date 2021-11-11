<?php

namespace modules\product\src\abac;

use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\order\OrderStatus;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productType\ProductTypeQuery;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\CasesStatus;
use sales\helpers\setting\SettingHelper;

class ProductQuoteAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'product/product-quote/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_DECLINE_REPROTECTION_QUOTE = self::NS . 'act/reprotection_quote/decline'; /*TODO:: to remove after release 3.34*/
    public const ACT_VIEW_DETAILS_REFUND_QUOTE = self::NS . 'act/refund_quote/details'; /*TODO:: to remove */
    public const ACT_VIEW_DETAILS = self::NS . 'act/view_details'; /*TODO:: to remove */
    public const ACT_ADD_CHANGE = self::NS . 'act/add_change'; /*TODO:: to remove */
    public const ACT_CREATE_VOL_REFUND = self::NS . 'act/create_voluntary_quote_refund'; /*TODO:: to remove */
    public const ACT_PRODUCT_QUOTE_REMOVE = self::NS . 'act/remove'; /*TODO:: to remove */

    /** OBJECT PERMISSION */
    public const OBJ_PRODUCT_QUOTE = self::NS . 'obj/product-quote';

    public const OBJECT_LIST = [
        self::ACT_DECLINE_REPROTECTION_QUOTE => self::ACT_DECLINE_REPROTECTION_QUOTE, /*TODO:: to remove*/
        self::ACT_VIEW_DETAILS_REFUND_QUOTE => self::ACT_VIEW_DETAILS_REFUND_QUOTE,
        self::ACT_VIEW_DETAILS => self::ACT_VIEW_DETAILS, /*TODO:: to remove*/
        self::ACT_ADD_CHANGE => self::ACT_ADD_CHANGE,     /*TODO:: to remove*/
        self::ACT_CREATE_VOL_REFUND => self::ACT_CREATE_VOL_REFUND, /*TODO:: to remove*/
        self::ACT_PRODUCT_QUOTE_REMOVE => self::ACT_PRODUCT_QUOTE_REMOVE, /*TODO:: to remove*/

        self::OBJ_PRODUCT_QUOTE => self::OBJ_PRODUCT_QUOTE
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_READ  = 'read';
    public const ACTION_CREATE  = 'create';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_DELETE  = 'delete';

    public const ACTION_DECLINE_RE_PROTECTION_QUOTE = 'declineReProtectionQuote'; /*TODO:: to remove was moved to RelatedProductQuoteAbacObject.php*/
    public const ACTION_ACCESS_DETAILS = 'accessDetails';
    public const ACTION_CREATE_CHANGE = 'createChange';
    public const ACTION_CREATE_VOL_REFUND = 'createVoluntaryRefundQuote';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_DECLINE_REPROTECTION_QUOTE => [self::ACTION_ACCESS], /*TODO:: to remove*/
        self::ACT_VIEW_DETAILS_REFUND_QUOTE => [self::ACTION_ACCESS],
        self::ACT_VIEW_DETAILS => [self::ACTION_ACCESS], /*TODO:: to remove*/
        self::ACT_ADD_CHANGE => [self::ACTION_ACCESS], /*TODO:: to remove*/
        self::ACT_CREATE_VOL_REFUND => [self::ACTION_ACCESS], /*TODO:: to remove*/
        self::ACT_PRODUCT_QUOTE_REMOVE => [self::ACTION_ACCESS], /*TODO:: to remove*/

        self::OBJ_PRODUCT_QUOTE => [
            self::ACTION_DECLINE_RE_PROTECTION_QUOTE,
            self::ACTION_ACCESS_DETAILS,
            self::ACTION_CREATE_CHANGE,
            self::ACTION_CREATE_VOL_REFUND,
            self::ACTION_DELETE
        ]
    ];

    protected const ATTR_REPROTECTION_QUOTE_IS_NEW = [
        'optgroup' => 'PRODUCT QUOTE',
        'id' => self::NS . 'is_new',
        'field' => 'is_new',
        'label' => 'Is New',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        //'validation' => ['allow_empty_value' => true],
        'operators' =>  [self::OP_EQUAL2]
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
        self::ACT_DECLINE_REPROTECTION_QUOTE    => [self::ATTR_REPROTECTION_QUOTE_IS_NEW], /*TODO:: to remove*/
        /*TODO:: to remove ACT_VIEW_DETAILS*/
        self::ACT_VIEW_DETAILS    => [
            self::ATTR_PRODUCT_QUOTE_OWNER,
            self::ATTR_ORDER_OWNER,
            self::ATTR_CASE_OWNER,
            self::ATTR_IS_COMMON_GROUP,
            self::ATTR_IS_AUTOMATE_CASE,
            self::ATTR_IS_PQ_CHANGEABLE,
            self::ATTR_HAS_PQR_ACTIVE,
            self::ATTR_HAS_PQC_ACTIVE
        ],
        /*TODO:: to remove ACT_ADD_CHANGE*/
        self::ACT_ADD_CHANGE      => [
            self::ATTR_PRODUCT_QUOTE_OWNER,
            self::ATTR_ORDER_OWNER,
            self::ATTR_CASE_OWNER,
            self::ATTR_IS_COMMON_GROUP,
            self::ATTR_IS_AUTOMATE_CASE,
            self::ATTR_IS_PQ_CHANGEABLE,
            self::ATTR_HAS_PQR_ACTIVE,
            self::ATTR_HAS_PQC_ACTIVE
        ],
        /*TODO:: to remove ACT_CREATE_VOL_REFUND*/
        self::ACT_CREATE_VOL_REFUND => [
            self::ATTR_PRODUCT_QUOTE_OWNER,
            self::ATTR_ORDER_OWNER,
            self::ATTR_CASE_OWNER,
            self::ATTR_IS_COMMON_GROUP,
            self::ATTR_IS_AUTOMATE_CASE,
            self::ATTR_IS_PQ_CHANGEABLE,
            self::ATTR_HAS_PQR_ACTIVE,
            self::ATTR_HAS_PQC_ACTIVE
        ],
        /*TODO:: to remove ACT_PRODUCT_QUOTE_REMOVE*/
        self::ACT_PRODUCT_QUOTE_REMOVE => [
            self::ATTR_PRODUCT_QUOTE_OWNER,
            self::ATTR_ORDER_OWNER,
            self::ATTR_CASE_OWNER,
            self::ATTR_IS_COMMON_GROUP,
            self::ATTR_IS_AUTOMATE_CASE,
            self::ATTR_IS_PQ_CHANGEABLE,
            self::ATTR_HAS_PQR_ACTIVE,
            self::ATTR_HAS_PQC_ACTIVE
        ],

        self::OBJ_PRODUCT_QUOTE => [
            self::ATTR_REPROTECTION_QUOTE_IS_NEW,
            self::ATTR_PRODUCT_QUOTE_OWNER,
            self::ATTR_ORDER_OWNER,
            self::ATTR_CASE_OWNER,
            self::ATTR_IS_COMMON_GROUP,
            self::ATTR_IS_AUTOMATE_CASE,
            self::ATTR_IS_PQ_CHANGEABLE,
            self::ATTR_HAS_PQR_ACTIVE,
            self::ATTR_HAS_PQC_ACTIVE
        ]
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
        $projects = Project::getList();

        $attrStatusList = self::ATTR_PRODUCT_QUOTE_STATUS;
        $attrProductTypeList = self::ATTR_PRODUCT_TYPE;
        $attrProductProjectList = self::ATTR_PRODUCT_PROJECT;
        $attrOrderProjectList = self::ATTR_ORDER_PROJECT;
        $attrOrderStatusList = self::ATTR_ORDER_STATUS;
        $attrOrderPayStatusList = self::ATTR_ORDER_PAY_STATUS;
        $attrOrderTypeList = self::ATTR_ORDER_TYPE;
        $attrCaseCategoryList = self::ATTR_CASE_CATEGORY;
        $attrCaseProjectList = self::ATTR_CASE_PROJECT;
        $attrCaseStatusList = self::ATTR_CASE_STATUS;

        $attrStatusList['values'] = ProductQuoteStatus::getList();
        $attrProductTypeList['values'] = ProductTypeQuery::getListAll();
        $attrProductProjectList['values'] = $projects;
        $attrOrderProjectList['values'] = $projects;
        $attrOrderStatusList['values'] = OrderStatus::getList();
        $attrOrderPayStatusList['values'] = OrderPayStatus::getList();
        $attrOrderTypeList['values'] = OrderSourceType::LIST;
        $attrCaseCategoryList['values'] = CaseCategory::getList();
        $attrCaseProjectList['values'] = $projects;
        $attrCaseStatusList['values'] = CasesStatus::STATUS_LIST;

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        /*TODO:: remove $attributeList[self::ACT_VIEW_DETAILS]*/
        $attributeList[self::ACT_VIEW_DETAILS][] = $attrStatusList;
        $attributeList[self::ACT_VIEW_DETAILS][] = $attrProductTypeList;
        $attributeList[self::ACT_VIEW_DETAILS][] = $attrProductProjectList;
        $attributeList[self::ACT_VIEW_DETAILS][] = $attrOrderProjectList;
        $attributeList[self::ACT_VIEW_DETAILS][] = $attrOrderStatusList;
        $attributeList[self::ACT_VIEW_DETAILS][] = $attrOrderPayStatusList;
        $attributeList[self::ACT_VIEW_DETAILS][] = $attrOrderTypeList;
        $attributeList[self::ACT_VIEW_DETAILS][] = $attrCaseCategoryList;
        $attributeList[self::ACT_VIEW_DETAILS][] = $attrCaseProjectList;
        $attributeList[self::ACT_VIEW_DETAILS][] = $attrCaseStatusList;
        /*TODO:: remove $attributeList[self::ACT_ADD_CHANGE]*/
        $attributeList[self::ACT_ADD_CHANGE][] = $attrStatusList;
        $attributeList[self::ACT_ADD_CHANGE][] = $attrProductTypeList;
        $attributeList[self::ACT_ADD_CHANGE][] = $attrProductProjectList;
        $attributeList[self::ACT_ADD_CHANGE][] = $attrOrderProjectList;
        $attributeList[self::ACT_ADD_CHANGE][] = $attrOrderStatusList;
        $attributeList[self::ACT_ADD_CHANGE][] = $attrOrderPayStatusList;
        $attributeList[self::ACT_ADD_CHANGE][] = $attrOrderTypeList;
        $attributeList[self::ACT_ADD_CHANGE][] = $attrCaseCategoryList;
        $attributeList[self::ACT_ADD_CHANGE][] = $attrCaseProjectList;
        $attributeList[self::ACT_ADD_CHANGE][] = $attrCaseStatusList;
        /*TODO:: remove $attributeList[self::ACT_CREATE_VOL_REFUND]*/
        $attributeList[self::ACT_CREATE_VOL_REFUND][] = $attrStatusList;
        $attributeList[self::ACT_CREATE_VOL_REFUND][] = $attrProductTypeList;
        $attributeList[self::ACT_CREATE_VOL_REFUND][] = $attrProductProjectList;
        $attributeList[self::ACT_CREATE_VOL_REFUND][] = $attrOrderProjectList;
        $attributeList[self::ACT_CREATE_VOL_REFUND][] = $attrOrderStatusList;
        $attributeList[self::ACT_CREATE_VOL_REFUND][] = $attrOrderPayStatusList;
        $attributeList[self::ACT_CREATE_VOL_REFUND][] = $attrOrderTypeList;
        $attributeList[self::ACT_CREATE_VOL_REFUND][] = $attrCaseCategoryList;
        $attributeList[self::ACT_CREATE_VOL_REFUND][] = $attrCaseProjectList;
        $attributeList[self::ACT_CREATE_VOL_REFUND][] = $attrCaseStatusList;
        /*TODO:: remove $attributeList[self::ACT_PRODUCT_QUOTE_REMOVE]*/
        $attributeList[self::ACT_PRODUCT_QUOTE_REMOVE][] = $attrStatusList;
        $attributeList[self::ACT_PRODUCT_QUOTE_REMOVE][] = $attrProductTypeList;
        $attributeList[self::ACT_PRODUCT_QUOTE_REMOVE][] = $attrProductProjectList;
        $attributeList[self::ACT_PRODUCT_QUOTE_REMOVE][] = $attrOrderProjectList;
        $attributeList[self::ACT_PRODUCT_QUOTE_REMOVE][] = $attrOrderStatusList;
        $attributeList[self::ACT_PRODUCT_QUOTE_REMOVE][] = $attrOrderPayStatusList;
        $attributeList[self::ACT_PRODUCT_QUOTE_REMOVE][] = $attrOrderTypeList;
        $attributeList[self::ACT_PRODUCT_QUOTE_REMOVE][] = $attrCaseCategoryList;
        $attributeList[self::ACT_PRODUCT_QUOTE_REMOVE][] = $attrCaseProjectList;
        $attributeList[self::ACT_PRODUCT_QUOTE_REMOVE][] = $attrCaseStatusList;

        $attributeList[self::OBJ_PRODUCT_QUOTE][] = $attrStatusList;
        $attributeList[self::OBJ_PRODUCT_QUOTE][] = $attrProductTypeList;
        $attributeList[self::OBJ_PRODUCT_QUOTE][] = $attrProductProjectList;
        $attributeList[self::OBJ_PRODUCT_QUOTE][] = $attrOrderProjectList;
        $attributeList[self::OBJ_PRODUCT_QUOTE][] = $attrOrderStatusList;
        $attributeList[self::OBJ_PRODUCT_QUOTE][] = $attrOrderPayStatusList;
        $attributeList[self::OBJ_PRODUCT_QUOTE][] = $attrOrderTypeList;
        $attributeList[self::OBJ_PRODUCT_QUOTE][] = $attrCaseCategoryList;
        $attributeList[self::OBJ_PRODUCT_QUOTE][] = $attrCaseProjectList;
        $attributeList[self::OBJ_PRODUCT_QUOTE][] = $attrCaseStatusList;

        return $attributeList;
    }
}
