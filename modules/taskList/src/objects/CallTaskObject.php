<?php

namespace modules\taskList\src\objects;

use common\models\Department;
use common\models\EmailTemplateType;
use common\models\Project;

class CallTaskObject extends BaseTaskObject implements TaskObjectInterface
{
    public const OPTGROUP = 'Call';


    public string $duration;


    public const ATTR_DURATION = [
        'optgroup' => self::OPTGROUP,
        'id' => 'duration',
        'field' => 'duration',
        'label' => 'Duration',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '>=', '<=', '>', '<', self::OP_IN, self::OP_NOT_IN],
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
         'Duration' => self::ATTR_DURATION
    ];

    /**
     * @return array[]
     */
    public static function getObjectAttributeList(): array
    {
        /*$templateKey = self::ATTR_TEMPLATE_KEY;
        $templateKey['values'] = EmailTemplateType::getList(false, null);

        $project = self::ATTR_PROJECT_ID;
        $project['values'] = Project::getList();

        $department = self::ATTR_DEPARTMENT_ID;
        $department['values'] = Department::getList();*/

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        /*$attributeList[self::OBJ_PREVIEW_EMAIL][] = $templateKey;
        $attributeList[self::OBJ_PREVIEW_EMAIL][] = $project;
        $attributeList[self::OBJ_PREVIEW_EMAIL][] = $department;*/
        return $attributeList;
    }
}
