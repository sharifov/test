<?php

namespace modules\qaTask\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class QaTaskAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'qa-task/qa-task/';

    /** --------------- PERMISSIONS --------------------------- */

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    public const ACT_USER_ASSIGN = self::NS . 'act/user_assign';

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_ASSIGN  = 'assign';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::ACT_USER_ASSIGN => self::ACT_USER_ASSIGN
    ];

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_USER_ASSIGN => [self::ACTION_ACCESS]
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

        return $attributeList;
    }
}
