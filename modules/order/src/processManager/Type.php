<?php

namespace modules\order\src\processManager;

class Type
{
    public const PHONE_TO_BOOK = 1;
    public const CLICK_TO_BOOK = 2;

    public const LIST = [
        self::PHONE_TO_BOOK => 'Phone to Book',
        self::CLICK_TO_BOOK => 'Click to Book',
    ];
}
