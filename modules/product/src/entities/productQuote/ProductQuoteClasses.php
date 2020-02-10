<?php

namespace modules\product\src\entities\productQuote;

use modules\flight\models\FlightQuote;
use modules\hotel\models\HotelQuote;
use modules\product\src\entities\productType\ProductType;

class ProductQuoteClasses
{
    private const CLASSES = [
        ProductType::PRODUCT_FLIGHT => FlightQuote::class,
        ProductType::PRODUCT_HOTEL => HotelQuote::class,
    ];

    public static function getClass(int $type): string
    {
        if (!isset(self::CLASSES[$type])) {
            throw new \DomainException('Undefined Product Quote Class type');
        }

        return self::CLASSES[$type];
    }
}
