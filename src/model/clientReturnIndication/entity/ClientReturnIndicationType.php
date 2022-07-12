<?php

namespace src\model\clientReturnIndication\entity;

class ClientReturnIndicationType
{
    public const RETURN_CUSTOMER = 1;
    public const COMPANY_RETURN_CUSTOMER = 2;
    public const DIAMOND_CUSTOMER = 3;

    private const LIST = [
        self::RETURN_CUSTOMER => 'Return Customer',
        self::COMPANY_RETURN_CUSTOMER => 'Company Return Customer',
        self::DIAMOND_CUSTOMER => 'Diamond Customer'
    ];

    public static function getList(): array
    {
        return self::LIST;
    }
}
