<?php

namespace modules\taskList\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class TaskListAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    public const NS = 'task-list/task-list/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';
    /** UI PERMISSION */
    public const UI_ASSIGN  = self::NS . 'ui/access';
    /** ACTION PERMISSION */
    public const ACT_MY_TASK_LIST = self::NS . 'act/my_task_list';

    /** ACTIONS */
    public const ACTION_ACCESS = 'access';

    /**
     * OBJECT LIST
     * @return string[]
     */
    public static function getObjectList(): array
    {
        return [
            self::UI_ASSIGN => self::UI_ASSIGN,
            self::ACT_MY_TASK_LIST => self::ACT_MY_TASK_LIST,
        ];
    }

    /**
     * ACTION LIST
     * @return string[]
     */
    public static function getObjectActionList(): array
    {
        return [
            self::UI_ASSIGN => [self::ACTION_ACCESS],
            self::ACT_MY_TASK_LIST => [self::ACTION_ACCESS],
        ];
    }

    /**
     * @return array
     */
    public static function getObjectAttributeList(): array
    {
        return [];
    }
}
