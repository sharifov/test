<?php

namespace modules\objectTask\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class ObjectTaskObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'objectTask/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_OBJECT_TASK_LIST = self::NS . 'act/object_task_list';
    public const ACT_OBJECT_TASK_SCENARIO = self::NS . 'act/object_task_scenario';
    public const ACT_OBJECT_TASK_STATUS_LOG = self::NS . 'act/object_task_status_log';

    /** OBJECT LIST */
    public const OBJECT_LIST = [
        self::ACT_OBJECT_TASK_LIST => self::ACT_OBJECT_TASK_LIST,
        self::ACT_OBJECT_TASK_SCENARIO => self::ACT_OBJECT_TASK_SCENARIO,
        self::ACT_OBJECT_TASK_STATUS_LOG => self::ACT_OBJECT_TASK_STATUS_LOG,
    ];

    /** ACTIONS */
    public const ACTION_ACCESS = 'access';
    public const ACTION_UPDATE = 'update';
    public const ACTION_CREATE = 'create';
    public const ACTION_DELETE = 'delete';

    /** ACTION LIST */
    public const OBJECT_ACTION_LIST = [
        self::ACT_OBJECT_TASK_LIST => [self::ACTION_ACCESS, self::ACTION_UPDATE, self::ACTION_DELETE],
        self::ACT_OBJECT_TASK_SCENARIO => [self::ACTION_ACCESS, self::ACTION_CREATE, self::ACTION_UPDATE, self::ACTION_DELETE],
        self::ACT_OBJECT_TASK_STATUS_LOG => [self::ACTION_ACCESS, self::ACTION_CREATE, self::ACTION_UPDATE, self::ACTION_DELETE],
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
