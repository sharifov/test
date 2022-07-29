<?php

namespace modules\taskList\src\objects\sms;

use common\models\Project;
use modules\taskList\src\objects\BaseTaskObject;
use modules\taskList\src\objects\TargetObjectList;
use modules\taskList\src\objects\TaskObjectInterface;

class SmsTaskObject extends BaseTaskObject implements TaskObjectInterface
{
    /** NAMESPACE */
    private const NS = 'sms/';

    public const OPTGROUP_CALL = 'Sms';

    public const OBJ_SMS = 'sms';

    public const FIELD_PROJECT_KEY = self::OBJ_SMS . '.' . 'project_key';

    public const OBJECT_OPTION_LIST = [
    ];

    public const TARGET_OBJECT_LIST = [
        TargetObjectList::TARGET_OBJ_LEAD
    ];

    protected const ATTR_PROJECT_KEY = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::NS . self::FIELD_PROJECT_KEY,
        'field' => self::FIELD_PROJECT_KEY,
        'label' => 'Sms Project',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' => [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
        'icon' => 'fa fa-list',
    ];

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        0 => self::ATTR_PROJECT_KEY,
    ];

    /**
     * @return array[]
     */
    public static function getObjectAttributeList(): array
    {
        $project = self::ATTR_PROJECT_KEY;
        $project['values'] = Project::getKeyList();

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        $attributeList[0] = $project;
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
