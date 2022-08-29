<?php

namespace modules\userStats\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class UserStatsAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'user-stats/user-stats/';

    /** OBJECT PERMISSION */
    public const OBJ_USER_STATS = self::NS . 'obj/user-stats';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::OBJ_USER_STATS => self::OBJ_USER_STATS,
    ];

    /** --------------- ACTIONS ------------------------------- */
    public const ACTION_ACCESS  = 'access';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::OBJ_USER_STATS => [self::ACTION_ACCESS],
    ];

    protected const ATTR_USER_STATS_USER_ID = [
        'optgroup' => 'USER_STATS',
        'id' => self::NS . 'userId',
        'field' => 'userId',
        'label' => 'User ID',
        'type' => self::ATTR_TYPE_INTEGER,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    /** --------------- ATTRIBUTE LIST ------------------------ */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::OBJ_USER_STATS  =>  [
            self::ATTR_USER_STATS_USER_ID
        ]
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
        return self::OBJECT_ATTRIBUTE_LIST;
    }
}
