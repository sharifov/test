<?php

namespace modules\product\src\entities\productQuoteObjectRefund;

use modules\flight\src\services\flightQuoteRefund\FlightQuoteRefundService;
use modules\product\src\entities\productType\ProductType;

class QuoteObjectRefundServiceClasses
{
    private const CLASSES = [
        ProductType::PRODUCT_FLIGHT => FlightQuoteRefundService::class,
    ];

    public static function getClass(int $type): string
    {
        if (!isset(self::CLASSES[$type])) {
            throw new \DomainException('Undefined Product Quote Class type');
        }

        return self::CLASSES[$type];
    }
}
