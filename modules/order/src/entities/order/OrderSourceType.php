<?php

namespace modules\order\src\entities\order;

class OrderSourceType
{
    public const P2B = 1;
    public const C2B = 2;
    public const MANUAL = 3;

    public const LIST = [
        self::P2B => 'P2B',
        self::C2B => 'C2B',
        self::MANUAL => 'Manual'
    ];
}
