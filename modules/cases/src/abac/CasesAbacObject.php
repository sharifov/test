<?php

namespace modules\cases\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class CasesAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'cases/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** LOGIC PERMISSION */
    public const LOGIC_CLIENT_DATA   = self::NS . 'logic/client_data';

    public const OBJECT_LIST = [
        self::LOGIC_CLIENT_DATA => self::LOGIC_CLIENT_DATA,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_MASK    = 'mask';
    public const ACTION_UNMASK    = 'unmask';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::LOGIC_CLIENT_DATA  => [self::ACTION_UNMASK],
    ];

    protected const ATTR_CASE_IS_OWNER = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'is_owner',
        'field' => 'is_owner',
        'label' => 'Is Owner',

        /*'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [1 => 'Unmask', 2 => 'Mask'],
        'operators' =>  [self::OP_EQUAL2]*/

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        //'validation' => ['allow_empty_value' => true],
        'operators' =>  [self::OP_EQUAL2]
    ];

    /** --------------- ATTRIBUTE LIST --------------------------- */
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

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::LOGIC_CLIENT_DATA][] = self::ATTR_CASE_IS_OWNER;

        return $attributeList;
    }
}
