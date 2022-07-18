<?php

namespace modules\taskList\src\objects\call;

use common\models\Department;
use common\models\Project;
use modules\taskList\src\objects\BaseTaskObject;
use modules\taskList\src\objects\TargetObjectList;
use modules\taskList\src\objects\TaskObjectInterface;

class CallTaskObject extends BaseTaskObject implements TaskObjectInterface
{
    /** NAMESPACE */
    private const NS = 'call/';

    public const OPTGROUP_CALL = 'Call';

    public const OBJ_CALL = 'call';

    public const FIELD_PROJECT_KEY       = self::OBJ_CALL . '.' . 'project_key';
    public const FIELD_DEPARTMENT_ID    = self::OBJ_CALL . '.' . 'department_id';
    public const FIELD_DURATION         = self::OBJ_CALL . '.' . 'duration';


    public const OBJECT_OPTION_LIST = [
        'workTimeStart' => ['label' => 'Work Time Start', 'type' => self::ATTR_TYPE_TIME, 'value' => '00:00'],
        'workTimeEnd'   => ['label' => 'Work Time End', 'type' => self::ATTR_TYPE_TIME, 'value' => '00:00'],
    ];

    public const TARGET_OBJECT_LIST = [
        TargetObjectList::TARGET_OBJ_LEAD,
        TargetObjectList::TARGET_OBJ_CASE,
    ];


    protected const ATTR_PROJECT_KEY = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::NS . self::FIELD_PROJECT_KEY,
        'field' => self::FIELD_PROJECT_KEY,
        'label' => 'Call Project',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
        'icon' => 'fa fa-list',
    ];

    protected const ATTR_DEPARTMENT_ID = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::NS . self::FIELD_DEPARTMENT_ID,
        'field' => self::FIELD_DEPARTMENT_ID,
        'label' => 'Call Department',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
        'icon' => 'fa fa-list',
    ];

    public const ATTR_DURATION = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::FIELD_DURATION,
        'field' => self::FIELD_DURATION,
        'label' => 'Call Duration',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '>=', '<=', '>', '<'], //, self::OP_IN, self::OP_NOT_IN
        'validation' => ['min' => 0, 'max' => 100000, 'step' => 1],
        'description' => 'Call Duration',


        /*'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN]*/
    ];


    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        0 => self::ATTR_DURATION,
        1 => self::ATTR_PROJECT_KEY,
        2 => self::ATTR_DEPARTMENT_ID
    ];

    /**
     * @return array[]
     */
    public static function getObjectAttributeList(): array
    {
        /*$templateKey = self::ATTR_TEMPLATE_KEY;
        $templateKey['values'] = EmailTemplateType::getList(false, null);
        */

        $project = self::ATTR_PROJECT_KEY;
        $project['values'] = Project::getKeyList();

        $department = self::ATTR_DEPARTMENT_ID;
        $department['values'] = Department::getList();

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        $attributeList[1] = $project;
        $attributeList[2] = $department;
        return $attributeList;
    }

    /**
     * @return array[]
     */
    public static function getObjectOptionList(): array
    {
        return array_merge(self::OBJECT_OPTION_LIST, parent::DEFAULT_OBJECT_OPTION_LIST);
    }

    /**
     * @return array[]
     */
    public static function getTargetObjectList(): array
    {
        return TargetObjectList::getTargetObjectListByIds(self::TARGET_OBJECT_LIST);
    }
}
