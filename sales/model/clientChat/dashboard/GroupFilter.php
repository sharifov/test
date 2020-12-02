<?php

namespace sales\model\clientChat\dashboard;

class GroupFilter
{
    public const NOTHING = -1;
    public const MY = 1;
    public const OTHER = 2;
    public const FREE_TO_TAKE = 3;
    public const TEAM_CHATS = 4;

    public const LIST = [
        self::NOTHING => 'Nothing',
        self::MY => 'My Chats',
        self::OTHER => 'Other Chats',
        self::FREE_TO_TAKE => 'Inbox',
        self::TEAM_CHATS => 'Team Chats',
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

    public static function isNothing(int $value): bool
    {
        return $value === self::NOTHING;
    }

    public static function isTeamChats(int $value): bool
    {
        return $value === self::TEAM_CHATS;
    }

    public static function isValid(int $value): bool
    {
        return array_key_exists($value, self::LIST);
    }
}
