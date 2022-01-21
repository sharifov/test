<?php

namespace src\model\userStatDay\entity;

class UserStatDayKey
{
    public const GROSS_PROFIT = 1;
    public const CONVERSION_PERCENT = 2;

    private const LIST = [
        self::GROSS_PROFIT => 'Gross Profit',
        self::CONVERSION_PERCENT => 'Conversion percent',
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
