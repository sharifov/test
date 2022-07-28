<?php

namespace modules\taskList\src\objects\sms;

use common\models\Project;
use common\models\SmsTemplateType;
use modules\taskList\src\objects\BaseTaskObject;
use modules\taskList\src\objects\TargetObjectList;
use modules\taskList\src\objects\TaskObjectInterface;

class SmsTaskObject extends BaseTaskObject implements TaskObjectInterface
{
    /** NAMESPACE */
    private const NS = 'sms/';

    public const OPTGROUP_SMS = 'Sms';

    public const OBJ_SMS = 'sms';

    public const FIELD_PROJECT_KEY = self::OBJ_SMS . '.' . 'project_key';
    public const FIELD_TEMPLATE_TYPE_KEY = self::OBJ_SMS . '.' . 'template_type_key';

    public const OBJECT_OPTION_LIST = [
    ];

    public const TARGET_OBJECT_LIST = [
        TargetObjectList::TARGET_OBJ_LEAD
    ];

    protected const ATTR_PROJECT_KEY = [
        'optgroup' => self::OPTGROUP_SMS,
        'id' => self::NS . self::FIELD_PROJECT_KEY,
        'field' => self::FIELD_PROJECT_KEY,
        'label' => 'Sms Project',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' => [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
        'icon' => 'fa fa-list',
    ];

    protected const ATTR_TEMPLATE_TYPE_KEY = [
        'optgroup' => self::OPTGROUP_SMS,
        'id' => self::NS . self::FIELD_TEMPLATE_TYPE_KEY,
        'field' => self::FIELD_TEMPLATE_TYPE_KEY,
        'label' => 'SMS Template Type',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' => [self::OP_IN, self::OP_NOT_IN],
        'icon' => 'fa fa-list',
    ];

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        0 => self::ATTR_PROJECT_KEY,
        1 => self::ATTR_TEMPLATE_TYPE_KEY
    ];

    /**
     * @return array[]
     */
    public static function getObjectAttributeList(): array
    {
        $project = self::ATTR_PROJECT_KEY;
        $project['values'] = Project::getKeyList();

        $templateType = self::ATTR_TEMPLATE_TYPE_KEY;
        $templateType['values'] = SmsTemplateType::getKeyList(false);

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        $attributeList[0] = $project;
        $attributeList[1] = $templateType;
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
