<?php

namespace modules\cases\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class CasesAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'cases/view/';

    /** LOGIC PERMISSION */
    public const LOGIC_DISABLE_CLIENT_DATA_MASK   = self::NS . 'logic/disable_client_data_mask';

    public const OBJECT_LIST = [
        self::LOGIC_DISABLE_CLIENT_DATA_MASK => self::LOGIC_DISABLE_CLIENT_DATA_MASK,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_SHOW    = 'show';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::LOGIC_DISABLE_CLIENT_DATA_MASK  => [self::ACTION_SHOW],
    ];

    protected const ATTR_CASE_OWNER = [
        'optgroup' => 'CASE - DATA PRIVACY',
        'id' => self::NS . 'owner',
        'field' => 'owner',
        'label' => 'Is Owner',

        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [1 => 'Unmask', 2 => 'Mask'],
        'operators' =>  [self::OP_EQUAL2]

        /*'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_CHECKBOX,
        'values' => [1 => 'Unmask'],
        'multiple' => false,
        'validation' => ['allow_empty_value' => true],
        'operators' =>  [self::OP_EQUAL2, self::OP_IN]*/
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
        $attrOwner = self::ATTR_CASE_OWNER;
        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::LOGIC_DISABLE_CLIENT_DATA_MASK][] = $attrOwner;

        return $attributeList;
    }
}
