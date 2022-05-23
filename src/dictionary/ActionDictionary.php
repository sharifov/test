<?php

namespace src\dictionary;

class ActionDictionary
{
    public const ACTION_ADD = 'add';
    public const ACTION_REPLACE   = 'replace';
    public const ACTION_REMOVE = 'remove';

    public const BASE_ACTION_LIST = [
        self::ACTION_ADD => 'Add',
        self::ACTION_REPLACE => 'Replace',
        self::ACTION_REMOVE => 'Remove'
    ];
}
