<?php

/**
 * Created by PhpStorm.
 * User: shakarim
 * Date: 6/29/22
 * Time: 8:50 PM
 */

namespace src\model\callLog\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class CallLogAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'call-log/';

    /** ABAC OBJECTS */
    public const ALL = self::NS . '*';
    public const OBJECT_ACT_ALL = self::NS . 'act/*';
    public const OBJECT_ACT_INDEX = self::NS . 'act/index';
    public const OBJECT_ACT_CREATE = self::NS . 'act/create';
    public const OBJECT_ACT_VIEW = self::NS . 'act/view';
    public const OBJECT_ACT_UPDATE = self::NS . 'act/update';
    public const OBJECT_ACT_DELETE = self::NS . 'act/delete';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::OBJECT_ACT_ALL => self::OBJECT_ACT_ALL,
        self::OBJECT_ACT_INDEX => self::OBJECT_ACT_INDEX,
        self::OBJECT_ACT_CREATE => self::OBJECT_ACT_CREATE,
        self::OBJECT_ACT_VIEW => self::OBJECT_ACT_VIEW,
        self::OBJECT_ACT_UPDATE => self::OBJECT_ACT_UPDATE,
        self::OBJECT_ACT_DELETE => self::OBJECT_ACT_DELETE
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS = 'access';
    public const ACTION_LISTEN = 'listen';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::OBJECT_ACT_ALL => [self::ACTION_ACCESS],
        self::OBJECT_ACT_INDEX => [self::ACTION_ACCESS, self::ACTION_LISTEN],
        self::OBJECT_ACT_CREATE => [self::ACTION_ACCESS],
        self::OBJECT_ACT_VIEW => [self::ACTION_ACCESS],
        self::OBJECT_ACT_UPDATE => [self::ACTION_ACCESS],
        self::OBJECT_ACT_DELETE => [self::ACTION_ACCESS]
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
        $attributes[self::OBJECT_ACT_INDEX] = array_merge(
            self::getExistAttributeList($attributes, self::OBJECT_ACT_INDEX),
            [
                [
                    'optgroup' => 'Call',
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
                    'optgroup' => 'Call',
                    'id' => self::NS . 'record_departments',
                    'field' => 'record_departments',
                    'label' => 'Department',
                    'type' => self::ATTR_TYPE_STRING,
                    'input' => self::ATTR_INPUT_SELECT,
                    'multiple' => false,
                    'values' => self::getDepartmentList(),
                    'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
                ]
            ]
        );

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
