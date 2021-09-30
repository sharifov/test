<?php

namespace sales\model\userStatDay\entity;

class UserStatDayKey
{
    public const GROSS_PROFIT = 1;

    private const LIST = [
        self::GROSS_PROFIT => 'Gross Profit'
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function getNameById(int $id): ?string
    {
        return self::LIST[$id] ?? null;
    }
}
