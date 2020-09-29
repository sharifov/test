<?php

namespace sales\model\clientChat\dashboard;

class ReadFilter
{
    public const ALL = 0;
    public const READ = 1;
    public const UNREAD = 2;

    public const LIST = [
        self::READ => 'Read',
        self::UNREAD => 'Unread',
    ];

    public static function isRead(int $value): bool
    {
        return $value === self::READ;
    }

    public static function isUnread(int $value): bool
    {
        return $value === self::UNREAD;
    }

    public static function isAll(int $value): bool
    {
        return $value === self::ALL;
    }

    public static function isValid(int $value): bool
    {
        return array_key_exists($value, self::LIST);
    }
}
