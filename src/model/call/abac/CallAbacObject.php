<?php

namespace src\model\call\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

/**
 * Class CallAbacObject
 */
class CallAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'call/call/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_ALL                        = self::NS . '*';
    public const ACT_DATA_ALLOW_LIST            = self::NS . 'act/data-allow-list';
    public const ACT_DATA_IS_TRUSTED            = self::NS . 'act/data-is-trusted';
    public const ACT_DATA_AUTO_CREATE_CASE_OFF  = self::NS . 'act/data-auto-create-case-off';
    public const ACT_DATA_AUTO_CREATE_LEAD_OFF  = self::NS . 'act/data-auto-create-lead-off';
    public const ACT_DATA_INVALID               = self::NS . 'act/data-invalid';
    public const OBJ_CALL_LOG                   = self::NS . 'obj/call-log';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::ACT_ALL                       => self::ACT_ALL,
        self::ACT_DATA_ALLOW_LIST           => self::ACT_DATA_ALLOW_LIST,
        self::ACT_DATA_IS_TRUSTED           => self::ACT_DATA_IS_TRUSTED,
        self::ACT_DATA_AUTO_CREATE_CASE_OFF => self::ACT_DATA_AUTO_CREATE_CASE_OFF,
        self::ACT_DATA_AUTO_CREATE_LEAD_OFF => self::ACT_DATA_AUTO_CREATE_LEAD_OFF,
        self::ACT_DATA_INVALID              => self::ACT_DATA_INVALID,
        self::OBJ_CALL_LOG                  => self::OBJ_CALL_LOG,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_VIEW  = 'view';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_DELETE  = 'delete';
    public const ACTION_TOGGLE_DATA  = 'toggle_data';
    public const ACTION_LISTEN_RECORD  = 'listen_record';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_ALL                       => [self::ACTION_UPDATE, self::ACTION_TOGGLE_DATA],
        self::ACT_DATA_ALLOW_LIST           => [self::ACTION_TOGGLE_DATA],
        self::ACT_DATA_IS_TRUSTED           => [self::ACTION_TOGGLE_DATA],
        self::ACT_DATA_AUTO_CREATE_CASE_OFF => [self::ACTION_TOGGLE_DATA],
        self::ACT_DATA_AUTO_CREATE_LEAD_OFF => [self::ACTION_TOGGLE_DATA],
        self::ACT_DATA_INVALID              => [self::ACTION_TOGGLE_DATA],
        self::OBJ_CALL_LOG                  => [self::ACTION_VIEW, self::ACTION_UPDATE, self:: ACTION_DELETE, self::ACTION_LISTEN_RECORD]
    ];

    public const OBJECT_ATTRIBUTE_LIST = [];

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
        $attributes = self::OBJECT_ATTRIBUTE_LIST;

        $callLogAttributes = [
            [
                'optgroup' => 'Call Log',
                'id' => self::NS . 'is_call_owner',
                'field' => 'is_call_owner',
                'label' => 'Is Record Owner',
                'type' => self::ATTR_TYPE_BOOLEAN,
                'input' => self::ATTR_INPUT_RADIO,
                'multiple' => false,
                'default_value' => true,
                'vertical' => true,
                'values' => ['true' => 'True', 'false' => 'False'],
                'operators' =>  [self::OP_EQUAL2]
            ],
            [
                'optgroup' => 'Call Log',
                'id' => self::NS . 'record_department',
                'field' => 'record_department',
                'label' => 'Department',
                'type' => self::ATTR_TYPE_STRING,
                'input' => self::ATTR_INPUT_SELECT,
                'multiple' => false,
                'values' => self::getDepartmentList(),
                'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2]
            ],
            [
                'optgroup' => 'Call Log',
                'id' => self::NS . 'type_id',
                'field' => 'type_id',
                'label' => 'Type',
                'type' => self::ATTR_TYPE_INTEGER,
                'input' => self::ATTR_INPUT_SELECT,
                'multiple' => false,
                'values' => \src\model\callLog\entity\callLog\CallLogType::getList(),
                'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2]
            ],
            [
                'optgroup' => 'Call Log',
                'id' => self::NS . 'project_id',
                'field' => 'project_id',
                'label' => 'Project',
                'type' => self::ATTR_TYPE_INTEGER,
                'input' => self::ATTR_INPUT_SELECT,
                'multiple' => false,
                'values' => \common\models\Project::getList(),
                'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2]
            ],
            [
                'optgroup' => 'Call Log',
                'id' => self::NS . 'category_id',
                'field' => 'category_id',
                'label' => 'Category',
                'type' => self::ATTR_TYPE_INTEGER,
                'input' => self::ATTR_INPUT_SELECT,
                'multiple' => false,
                'values' => \common\models\Call::SOURCE_LIST,
                'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2]
            ],
            [
                'optgroup' => 'Call Log',
                'id' => self::NS . 'status_id',
                'field' => 'status_id',
                'label' => 'Status',
                'type' => self::ATTR_TYPE_INTEGER,
                'input' => self::ATTR_INPUT_SELECT,
                'multiple' => false,
                'values' => \common\models\Call::STATUS_LIST,
                'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2]
            ]
        ];
        $attributes[self::OBJ_CALL_LOG] = array_merge(self::getExistAttributeList($attributes, self::OBJ_CALL_LOG), $callLogAttributes);

        return $attributes;
    }

    /**
     * @param array $list
     * @param String $action
     * @return array
     */
    protected static function getExistAttributeList(array $list, string $action): array
    {
        return isset($list[$action]) ? $list[$action] : [];
    }
}
