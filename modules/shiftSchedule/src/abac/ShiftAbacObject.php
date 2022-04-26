<?php

namespace modules\shiftSchedule\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

/**
 * Class ShiftAbacObject
 */
class ShiftAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'shift/shift/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_USER_SHIFT_ASSIGN = self::NS . 'act/user_shift_assign';
    public const ACT_MY_SHIFT_SCHEDULE = self::NS . 'act/my_shift_schedule';

    public const OBJ_USER_SHIFT_EVENT = self::NS . 'obj/user_shift_event';

    /** OBJECT LIST */
    public const OBJECT_LIST = [
        self::ACT_USER_SHIFT_ASSIGN => self::ACT_USER_SHIFT_ASSIGN,
        self::ACT_MY_SHIFT_SCHEDULE => self::ACT_MY_SHIFT_SCHEDULE,
        self::OBJ_USER_SHIFT_EVENT => self::OBJ_USER_SHIFT_EVENT,
        self::ALL => self::ALL,
    ];

    /** ACTIONS */
    public const ACTION_ACCESS = 'access';
    public const ACTION_UPDATE = 'update';

    public const ACTION_CREATE      = 'create';
    public const ACTION_READ        = 'read';
    public const ACTION_DELETE      = 'delete';

    /** ACTION LIST */
    public const OBJECT_ACTION_LIST = [
        self::ACT_USER_SHIFT_ASSIGN => [self::ACTION_ACCESS, self::ACTION_UPDATE],
        self::ACT_MY_SHIFT_SCHEDULE => [self::ACTION_ACCESS],
        self::OBJ_USER_SHIFT_EVENT => [self::ACTION_CREATE, self::ACTION_READ,
            self::ACTION_UPDATE, self::ACTION_DELETE],
        self::ALL => [self::ACTION_ACCESS, self::ACTION_CREATE, self::ACTION_READ,
            self::ACTION_UPDATE, self::ACTION_DELETE],
    ];

    /** ATTRIBUTE LIST */
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
