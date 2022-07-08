<?php

namespace modules\featureFlag\models\user;

use common\models\Department;
use common\models\Project;
use common\models\UserGroup;
use common\models\UserRole;
use kivork\FeatureFlag\Models\BaseFeatureFlagObject;
use kivork\FeatureFlag\Models\FeatureFlagInterface;
use yii\helpers\ArrayHelper;

class UserFeatureFlag extends BaseFeatureFlagObject implements FeatureFlagInterface
{
    private const NAME = 'User';
    private const TITLE = 'User Feature Flag';

    public const OBJ = 'user';

    public const FIELD_PROJECTS         = self::OBJ . '.' . 'projects';
    public const FIELD_DEPARTMENTS      = self::OBJ . '.' . 'departments';
    public const FIELD_USERNAME         = self::OBJ . '.' . 'username';
    public const FIELD_ROLES            = self::OBJ . '.' . 'roles';
    public const FIELD_GROUPS           = self::OBJ . '.' . 'groups';

    public const FIELD_MULTI_ROLES          = self::OBJ . '.' . 'multi_roles';
    public const FIELD_MULTI_GROUPS         = self::OBJ . '.' . 'multi_groups';
    public const FIELD_MULTI_DEPARTMENTS    = self::OBJ . '.' . 'multi_departments';


    public const OPTGROUP = 'USER';

    protected const ATTR_USER_USERNAME = [
        'optgroup' => self::OPTGROUP,
        'id' => self::FIELD_USERNAME,
        'field' => self::FIELD_USERNAME,
        'label' => 'Username',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN, self::OP_MATCH]
    ];

    protected const ATTR_USER_ROLES = [
        'optgroup' => self::OPTGROUP,
        'id' => self::FIELD_ROLES,
        'field' => self::FIELD_ROLES,
        'label' => 'User Roles',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    protected const ATTR_USER_MULTI_ROLES = [
        'optgroup' => self::OPTGROUP,
        'id' => self::FIELD_MULTI_ROLES,
        'field' => self::FIELD_ROLES,
        'label' => 'User Multi Roles',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_CONTAINS]
    ];

    protected const ATTR_USER_PROJECTS = [
        'optgroup' => self::OPTGROUP,
        'id' => self::FIELD_PROJECTS,
        'field' => self::FIELD_PROJECTS,
        'label' => 'User Projects',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    protected const ATTR_USER_DEPARTMENTS = [
        'optgroup' => self::OPTGROUP,
        'id' => self::FIELD_DEPARTMENTS,
        'field' => self::FIELD_DEPARTMENTS,
        'label' => 'User Departments',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    protected const ATTR_USER_MULTI_DEPARTMENTS = [
        'optgroup' => self::OPTGROUP,
        'id' => self::FIELD_MULTI_DEPARTMENTS,
        'field' => self::FIELD_DEPARTMENTS,
        'label' => 'User Multi Departments',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_CONTAINS]
    ];

    protected const ATTR_USER_GROUPS = [
        'optgroup' => self::OPTGROUP,
        'id' => self::FIELD_GROUPS,
        'field' => self::FIELD_GROUPS,
        'label' => 'User Groups',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    protected const ATTR_USER_MULTI_GROUPS = [
        'optgroup' => self::OPTGROUP,
        'id' => self::FIELD_MULTI_GROUPS,
        'field' => self::FIELD_GROUPS,
        'label' => 'User Multi Groups',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_CONTAINS]
    ];



    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const ATTRIBUTE_LIST = [
        self::ATTR_USER_USERNAME
    ];


    /**
     * @return string
     */
    public static function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public static function getTitle(): string
    {
        return self::TITLE;
    }

    /**
     * @return array[]
     */
    public static function getAttributeList(): array
    {
        $attributeList = self::ATTRIBUTE_LIST;

        $ur = self::ATTR_USER_ROLES;
        $mur = self::ATTR_USER_MULTI_ROLES;
        $ug = self::ATTR_USER_GROUPS;
        $mug = self::ATTR_USER_MULTI_GROUPS;
        $up = self::ATTR_USER_PROJECTS;
        $ud = self::ATTR_USER_DEPARTMENTS;
        $mud = self::ATTR_USER_MULTI_DEPARTMENTS;

        $ur['values'] = self::getUserRoleList();
        $mur['values'] = $ur['values'];
        $ug['values'] = self::getUserGroupList();
        $mug['values'] = $ug['values'];
        $up['values'] = self::getProjectList();
        $ud['values'] = self::getDepartmentList();
        $mud['values'] = $ud['values'];

        $attributeList[] = $ur;
        $attributeList[] = $mur;
        $attributeList[] = $ug;
        $attributeList[] = $mug;
        $attributeList[] = $up;
        $attributeList[] = $ud;
        $attributeList[] = $mud;

        return $attributeList;
    }


    /**
     * @return array
     */
    protected static function getUserRoleList(): array
    {
        return UserRole::getEnvListWOCache();
    }

    /**
     * @return array
     */
    protected static function getProjectList(): array
    {
        return Project::getEnvList();
    }

    /**
     * @return array
     */
    protected static function getUserGroupList(): array
    {
        return UserGroup::getEnvList();
    }

    /**
     * @return array
     */
    protected static function getDepartmentList(): array
    {
        return Department::getEnvList();
    }

    /**
     * @return array
     */
    public static function getFieldList(): array
    {
        $fieldList = [];
        $attrList = self::getAttributeList();
        if ($attrList) {
            $fieldList = ArrayHelper::getColumn($attrList, 'field');
        }
        return $fieldList;
    }
}
