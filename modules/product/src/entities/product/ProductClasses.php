<?php

namespace modules\product\src\entities\product;

use modules\flight\models\Flight;
use modules\hotel\models\Hotel;
use modules\product\src\entities\productType\ProductType;

class ProductClasses
{
    private const CLASSES = [
        ProductType::PRODUCT_FLIGHT => Flight::class,
        ProductType::PRODUCT_HOTEL => Hotel::class,
    ];

    public static function getClass(int $type): string
    {
        if (!isset(self::CLASSES[$type])) {
            throw new \DomainException('Undefined Product Class type');
        }

        return self::CLASSES[$type];
    }
}
