<?php

namespace modules\featureFlag\models\debug;

use common\models\Department;
use common\models\Project;
use kivork\FeatureFlag\Models\BaseFeatureFlagObject;
use kivork\FeatureFlag\Models\FeatureFlagInterface;
use src\helpers\app\AppHelper;
use yii\helpers\ArrayHelper;

class DebugFeatureFlag extends BaseFeatureFlagObject implements FeatureFlagInterface
{
    private const NAME = 'Debug';
    private const TITLE = 'Debug Feature Flag';

    public const OBJ_DEBUG = 'debug';

    public const FIELD_PROJECT_KEY       = self::OBJ_DEBUG . '.' . 'project_key';
    public const FIELD_DEPARTMENT_ID    = self::OBJ_DEBUG . '.' . 'department_id';
    public const FIELD_APP_TYPE         = self::OBJ_DEBUG . '.' . 'app_type';


    public const OPTGROUP = 'DATA';

    protected const ATTR_PROJECT_KEY = [
        'optgroup' => self::OPTGROUP,
        'id' => self::FIELD_PROJECT_KEY,
        'field' => self::FIELD_PROJECT_KEY,
        'label' => 'Project',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
        'icon' => 'fa fa-list',
    ];

    protected const ATTR_DEPARTMENT_ID = [
        'optgroup' => self::OPTGROUP,
        'id' => self::FIELD_DEPARTMENT_ID,
        'field' => self::FIELD_DEPARTMENT_ID,
        'label' => 'Department',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
        'icon' => 'fa fa-list',
    ];

    protected const ATTR_APP_TYPE = [
        'optgroup' => self::OPTGROUP,
        'id' => self::FIELD_APP_TYPE,
        'field' => self::FIELD_APP_TYPE,
        'label' => 'APP Type',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
        'icon' => 'fa fa-list',
    ];


    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
//        0 => self::ATTR_APP_TYPE,
//        1 => self::ATTR_PROJECT_KEY,
//        2 => self::ATTR_DEPARTMENT_ID
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
     * @return array
     */
    public static function getAttributeList(): array
    {
        $project = self::ATTR_PROJECT_KEY;
        $project['values'] = Project::getKeyList();

        $department = self::ATTR_DEPARTMENT_ID;
        $department['values'] = Department::getList();

        $appType = self::ATTR_APP_TYPE;
        $appType['values'] = AppHelper::getTypeList();

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        $defaultAttributeList = self::getDefaultAttributeList();

        $attributeList[0] = $appType;
        $attributeList[1] = $project;
        $attributeList[2] = $department;
        return array_merge($attributeList, $defaultAttributeList);
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
