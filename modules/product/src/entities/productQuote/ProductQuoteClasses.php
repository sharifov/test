<?php

namespace modules\product\src\entities\productQuote;

use modules\attraction\models\AttractionQuote;
use modules\cruise\src\entity\cruiseQuote\CruiseQuote;
use modules\flight\models\FlightQuote;
use modules\hotel\models\HotelQuote;
use modules\product\src\entities\productType\ProductType;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;

class ProductQuoteClasses
{
    private const CLASSES = [
        ProductType::PRODUCT_FLIGHT => FlightQuote::class,
        ProductType::PRODUCT_HOTEL => HotelQuote::class,
        ProductType::PRODUCT_RENT_CAR => RentCarQuote::class,
        ProductType::PRODUCT_CRUISE => CruiseQuote::class,
        ProductType::PRODUCT_ATTRACTION => AttractionQuote::class,
    ];

    public static function getClass(int $type): string
    {
        if (!isset(self::CLASSES[$type])) {
            throw new \DomainException('Undefined Product Quote Class type');
        }

        return self::CLASSES[$type];
    }
}
