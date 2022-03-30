<?php

namespace modules\cases\src\abac\saleList;

use common\models\Department;
use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\cases\src\abac\CasesAbacObject;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use src\entities\cases\CaseCategory;
use src\entities\cases\CasesStatus;

class SaleListAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    public const NS = 'case/case/';

    /** UI PERMISSION */

    public const UI_BLOCK_SALE_LIST = self::NS . 'ui/block/sale-list';

    public const OBJECT_LIST = [
        self::UI_BLOCK_SALE_LIST => self::UI_BLOCK_SALE_LIST,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ADD_CREDIT_CARD = 'Add Credit Card';
    public const ACTION_SEND_CC_INFO = 'Send CC Info';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::UI_BLOCK_SALE_LIST => [
            self::ACTION_ADD_CREDIT_CARD,
            self::ACTION_SEND_CC_INFO,
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
        'operators' => [self::OP_EQUAL2]
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
        'operators' => [self::OP_EQUAL2]
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
        'values' => CasesStatus::STATUS_LIST,
        'multiple' => false,
        'operators' => [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
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
        'operators' => [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
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
        'operators' => [self::OP_EQUAL2]
    ];

    public const ATTR_IS_COMMON_GROUP_OWNER = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'is_common_group_owner',
        'field' => 'is_common_group_owner',
        'label' => 'Is common group Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        //'validation' => ['allow_empty_value' => true],
        'operators' => [self::OP_EQUAL2]
    ];

    public const ATTR_IS_AUTOMATE = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'is_automate',
        'field' => 'is_automate',
        'label' => 'Is Automate',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        //'validation' => ['allow_empty_value' => true],
        'operators' => [self::OP_EQUAL2]
    ];

    public const ATTR_NEED_ACTION = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'need_action',
        'field' => 'need_action',
        'label' => 'Need Action',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        //'validation' => ['allow_empty_value' => true],
        'operators' => [self::OP_EQUAL2]
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
        'operators' => [self::OP_EQUAL2]
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
        $attributes = [
            self::UI_BLOCK_SALE_LIST => [
                self::ATTR_CASE_IS_OWNER,
                self::ATTR_IS_COMMON_GROUP_OWNER,
                self::ATTR_CASE_HAS_OWNER,
                self::ATTR_CASE_STATUS,
                self::ATTR_IS_AUTOMATE,
                self::ATTR_NEED_ACTION,
            ],
        ];

        $attrCategoryList = self::ATTR_CASE_CATEGORY;
        $attrCategoryList['values'] = CaseCategory::getList();
        $attributes[self::UI_BLOCK_SALE_LIST][] = $attrCategoryList;

        $attrCaseProjectName = CasesAbacObject::ATTR_CASE_PROJECT_NAME;
        $projectNames = Project::getList();
        $attrCaseProjectName['values'] = array_combine($projectNames, $projectNames);
        $attributes[self::UI_BLOCK_SALE_LIST][] = $attrCaseProjectName;

        $attrCaseDepartmentName = CasesAbacObject::ATTR_CASE_DEPARTMENT_NAME;
        $departmentNames = Department::getList();
        $attrCaseDepartmentName['values'] = array_combine($departmentNames, $departmentNames);
        $attributes[self::UI_BLOCK_SALE_LIST][] = $attrCaseDepartmentName;

        return $attributes;
    }
}
