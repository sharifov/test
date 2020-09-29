<?php

namespace sales\model\clientChat\entity;

class ClientChatTabGroups
{
    public const MY = 1;
    public const OTHER = 2;
    public const FREE_TO_TAKE = 3;

    public const LIST = [
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

    public static function isValid(int $value): bool
    {
        return array_key_exists($value, self::LIST);
    }
}
