<?php

namespace modules\taskList\src\objects\email;

use common\models\Department;
use common\models\EmailTemplateType;
use common\models\Project;
use modules\taskList\src\objects\BaseTaskObject;
use modules\taskList\src\objects\TargetObjectList;
use modules\taskList\src\objects\TaskObjectInterface;

class EmailTaskObject extends BaseTaskObject implements TaskObjectInterface
{
    /** NAMESPACE */
    private const NS = 'email/';

    public const OPTGROUP_EMAIL = 'Email';

    public const OBJ_EMAIL = 'email';

    public const FIELD_PROJECT_KEY = self::OBJ_EMAIL . '.' . 'project_key';
    public const FIELD_TEMPLATE_TYPE_KEY = self::OBJ_EMAIL . '.' . 'template_type_key';
    public const FIELD_EMAIL_HAS_CLIENT = self::OBJ_EMAIL . '.' . 'email_has_client';

    public const OBJECT_OPTION_LIST = [
        'testParams' => ['label' => 'Test', 'type' => self::ATTR_TYPE_INTEGER, 'value' => 123],
    ];

    public const TARGET_OBJECT_LIST = [
        TargetObjectList::TARGET_OBJ_LEAD
    ];

    protected const ATTR_PROJECT_KEY = [
        'optgroup' => self::OPTGROUP_EMAIL,
        'id' => self::NS . self::FIELD_PROJECT_KEY,
        'field' => self::FIELD_PROJECT_KEY,
        'label' => 'Email Project',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' => [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
        'icon' => 'fa fa-list',
    ];

    protected const ATTR_TEMPLATE_TYPE_KEY = [
        'optgroup' => self::OPTGROUP_EMAIL,
        'id' => self::NS . self::FIELD_TEMPLATE_TYPE_KEY,
        'field' => self::FIELD_TEMPLATE_TYPE_KEY,
        'label' => 'Email Template Type',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'operators' => [self::OP_IN, self::OP_NOT_IN],
        'icon' => 'fa fa-list',
    ];

    protected const ATTR_EMAIL_HAS_CLIENT = [
        'optgroup' => self::OPTGROUP_EMAIL,
        'id' => self::NS . self::FIELD_EMAIL_HAS_CLIENT,
        'field' => self::FIELD_EMAIL_HAS_CLIENT,
        'label' => 'Email Has Client',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2],
    ];

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        0 => self::ATTR_PROJECT_KEY,
        1 => self::ATTR_TEMPLATE_TYPE_KEY,
        2 => self::ATTR_EMAIL_HAS_CLIENT,
    ];

    /**
     * @return array[]
     */
    public static function getObjectAttributeList(): array
    {
        $project = self::ATTR_PROJECT_KEY;
        $project['values'] = Project::getKeyList();

        $templateType = self::ATTR_TEMPLATE_TYPE_KEY;
        $templateType['values'] = EmailTemplateType::getKeyList(false, null);

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
