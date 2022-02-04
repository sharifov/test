<?php

namespace modules\abac\src\object;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class AppAbac extends AbacBaseModel implements AbacInterface
{
    /** ALL PERMISSIONS */
    public const ALL = '*';

    public const OBJECT_LIST = [
        self::ALL => self::ALL
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ALL => [self::ACTION_ACCESS]
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
        return self::OBJECT_ATTRIBUTE_LIST;
    }
}
