<?php

namespace modules\order\src\entities\order;

class OrderSourceType
{
    public const P2B = 1;
    public const C2B = 2;
    public const C2BF = 3;
    public const MANUAL = 4;
    public const SALE = 5;

    public const LIST = [
        self::P2B => 'P2B',
        self::C2B => 'C2B',
        self::C2BF => 'C2B Facilitate',
        self::MANUAL => 'Manual',
        self::SALE => 'Sale',
    ];

    public const LIST_KEY_MAP = [
        'P2B' => self::P2B,
        'C2B' => self::C2B,
        'C2BF' => self::C2BF,
    ];
}
