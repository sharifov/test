<?php

namespace modules\cases\src\abac\update;

use common\models\Department;
use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\cases\src\abac\CasesAbacObject;
use src\entities\cases\CaseCategory;
use src\entities\cases\CasesSourceType;
use src\entities\cases\CasesStatus;

class UpdateAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    public const NS = 'case/case/';

    /** UI PERMISSION */
    public const UI_BLOCK_UPDATE_LIST = self::NS . 'ui/block/update';

    public const OBJECT_LIST = [
        self::UI_BLOCK_UPDATE_LIST => self::UI_BLOCK_UPDATE_LIST,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_EDIT_DEPARTMENT = 'Edit Department';
    public const ACTION_EDIT_CATEGORY = 'Edit Category';
    public const ACTION_EDIT_DESCRIPTION = 'Edit Description';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::UI_BLOCK_UPDATE_LIST => [
            self::ACTION_EDIT_DEPARTMENT,
            self::ACTION_EDIT_CATEGORY,
            self::ACTION_EDIT_DESCRIPTION,
        ],
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

    protected const ATTR_CASE_SRC_TYPE = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'source_type_id',
        'field' => 'source_type_id',
        'label' => 'Source Type',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
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
        $attrSrcTypeList = self::ATTR_CASE_SRC_TYPE;
        $attrSrcTypeList['values'] = CasesSourceType::getList();
        $attributes[self::UI_BLOCK_UPDATE_LIST][] = $attrSrcTypeList;

        $attrCategoryList = self::ATTR_CASE_CATEGORY;
        $attrCategoryList['values'] = CaseCategory::getList();
        $attributes[self::UI_BLOCK_UPDATE_LIST][] = $attrCategoryList;

        $attrCaseProjectName = CasesAbacObject::ATTR_CASE_PROJECT_NAME;
        $projectNames = Project::getList();
        $attrCaseProjectName['values'] = array_combine($projectNames, $projectNames);
        $attributes[self::UI_BLOCK_UPDATE_LIST][] = $attrCaseProjectName;

        $attrCaseDepartmentName = CasesAbacObject::ATTR_CASE_DEPARTMENT_NAME;
        $departmentNames = Department::getList();
        $attrCaseDepartmentName['values'] = array_combine($departmentNames, $departmentNames);
        $attributes[self::UI_BLOCK_UPDATE_LIST][] = $attrCaseDepartmentName;

        return $attributes;
    }
}
