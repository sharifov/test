<?php

namespace sales\model\clientChat\dashboard;

class ReadUnreadFilter
{
    public const ALL = 0;
    public const UNREAD = 1;

    public const LIST = [
        self::ALL => 'All',
        self::UNREAD => 'Unread',
    ];

    public static function isUnread(int $value): bool
    {
        return $value === self::UNREAD;
    }

    public static function isValid(int $value): bool
    {
        return array_key_exists($value, self::LIST);
    }
}
