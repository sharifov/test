<?php

namespace src\model\leadData\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use src\model\leadDataKey\entity\LeadDataKey;

/**
 * Class LeadDataAbacObject
 */
class LeadDataAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'lead-data/lead-data/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** UI PERMISSION */
    public const UI_INFO  = self::NS . 'ui/info';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::UI_INFO => self::UI_INFO,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_READ = 'read';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::UI_INFO => [self::ACTION_READ],
    ];

    /** --------------- ATTRIBUTES --------------------------- */
    protected const ATTR_DATA_KEY = [
        'optgroup' => 'Data Key',
        'id' => self::NS . 'dataKey',
        'field' => 'dataKey',
        'label' => 'Data Key',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2]
    ];

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::UI_INFO => [],
    ];

    public static function getObjectList(): array
    {
        return self::OBJECT_LIST;
    }

    public static function getObjectActionList(): array
    {
        return self::OBJECT_ACTION_LIST;
    }

    public static function getObjectAttributeList(): array
    {
        $attrDataKey = self::ATTR_DATA_KEY;
        $attrDataKey['values'] = LeadDataKey::getListCache(null);

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::UI_INFO][] = $attrDataKey;

        return $attributeList;
    }
}
