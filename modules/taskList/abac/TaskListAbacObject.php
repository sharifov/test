<?php

namespace modules\taskList\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class TaskListAbacObject extends AbacBaseModel implements AbacInterface
{
    public const NS = 'task-list/task-list/';

    public const ALL = self::NS . '*';

    public const UI_ASSIGN  = self::NS . 'ui/access';

    public const ACTION_ACCESS = 'access';

    public static function getObjectList(): array
    {
        return [
            self::UI_ASSIGN => self::UI_ASSIGN,
        ];
    }

    public static function getObjectActionList(): array
    {
        return [
            self::UI_ASSIGN => [self::ACTION_ACCESS],
        ];
    }

    public static function getObjectAttributeList(): array
    {
        return [];
    }
}
