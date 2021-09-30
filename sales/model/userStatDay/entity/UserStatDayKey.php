<?php

namespace sales\model\userStatDay\entity;

class UserStatDayKey
{
    public const GROSS_PROFIT = 1;

    private const LIST = [
        self::GROSS_PROFIT => 'Gross Profit'
    ];

    public function getList(): array
    {
        return self::LIST;
    }
}
