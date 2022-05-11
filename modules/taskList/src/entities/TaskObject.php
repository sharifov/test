<?php

namespace modules\taskList\src\entities;

class TaskObject
{
    public const OBJ_CALL   = 'call';
    public const OBJ_SMS    = 'sms';
    public const OBJ_EMAIL  = 'email';

    public const OBJ_LIST = [
        self::OBJ_CALL => 'Call',
        self::OBJ_SMS => 'SMS',
        self::OBJ_EMAIL => 'Email',
    ];

    /**
     * @return string[]
     */
    public static function getObjectList(): array
    {
        return self::OBJ_LIST;
    }
}
