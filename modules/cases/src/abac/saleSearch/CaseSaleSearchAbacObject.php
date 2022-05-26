<?php

namespace modules\cases\src\abac\saleSearch;

use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\cases\src\abac\CasesAbacObject;
use src\entities\cases\CasesStatus;

/**
 * Class CaseSaleSearchAbacObject
 */
class CaseSaleSearchAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    public const NS = CasesAbacObject::NS . 'sale-search/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** FORM PERMISSION */
    public const FORM_SALE_ID = self::NS . 'form/sale-id';

    /** OBJECT_LIST */
    public const OBJECT_LIST = [
        self::FORM_SALE_ID => self::FORM_SALE_ID,
    ];

    /** ACTIONS */
    public const ACTION_ACCESS  = 'access';

    /** ACTION LIST */
    public const OBJECT_ACTION_LIST = [
        self::FORM_SALE_ID => [self::ACTION_ACCESS],
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

    public const ATTR_CASE_STATUS = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'status_id',
        'field' => 'status_id',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => CasesStatus::STATUS_LIST,
        'multiple' => true,
        'operators' => [self::OP_IN]
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

    public static function getObjectAttributeList(): array
    {
        $attributes = [
            self::FORM_SALE_ID => [
                self::ATTR_CASE_IS_OWNER,
                self::ATTR_CASE_HAS_OWNER,
                self::ATTR_CASE_STATUS,
            ],
        ];

        $attrCaseProjectName = self::ATTR_CASE_PROJECT_NAME;
        $projectNames = Project::getList();
        $attrCaseProjectName['values'] = array_combine($projectNames, $projectNames);
        $attributes[self::FORM_SALE_ID][] = $attrCaseProjectName;

        return $attributes;
    }
}
