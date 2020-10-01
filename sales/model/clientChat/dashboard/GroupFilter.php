<?php

namespace sales\model\clientChat\dashboard;

class GroupFilter
{
    public const NOTHING = -1;
    public const ALL = 0;
    public const MY = 1;
    public const OTHER = 2;
    public const FREE_TO_TAKE = 3;

    public const SHORT_LIST = [
        self::MY => 'My Chats',
        self::OTHER => 'Other Chats',
        self::FREE_TO_TAKE => 'Free to take',
    ];

    public const FULL_LIST = [
        self::NOTHING => 'Nothing',
        self::ALL => 'All',
        self::MY => 'My Chats',
        self::OTHER => 'Other Chats',
        self::FREE_TO_TAKE => 'Free to take',
    ];

    public static function isMy(int $value): bool
    {
        return $value === self::MY;
    }

    public static function isOther(int $value): bool
    {
        return $value === self::OTHER;
    }

    public static function isFreeToTake(int $value): bool
    {
        return $value === self::FREE_TO_TAKE;
    }

    public static function isAll(int $value): bool
    {
        return $value === self::ALL;
    }

    public static function isNothing(int $value): bool
    {
        return $value === self::NOTHING;
    }

    public static function isValid(int $value): bool
    {
        return array_key_exists($value, self::FULL_LIST);
    }
}
