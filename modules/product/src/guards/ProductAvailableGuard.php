<?php

namespace modules\product\src\guards;

use modules\product\src\entities\productType\ProductType;
use modules\flight\FlightModule;
use modules\hotel\HotelModule;
use modules\product\src\exceptions\ProductCodeException;

class ProductAvailableGuard
{
    public static function check(int $productTypeId): void
    {
        if ($productTypeId === ProductType::PRODUCT_FLIGHT) {
            if (!class_exists(FlightModule::class)) {
                throw new \DomainException('Product Flight is unavailable', ProductCodeException::PRODUCT_FLIGHT_UNAVAILABLE);
            }
            return;
        }
        if ($productTypeId === ProductType::PRODUCT_HOTEL) {
            if (!class_exists(HotelModule::class)) {
                throw new \DomainException('Product Hotel is unavailable', ProductCodeException::PRODUCT_HOTEL_UNAVAILABLE);
            }
            return;
        }
        throw new \DomainException('Invalid product type', ProductCodeException::INVALID_PRODUCT_TYPE_GUARD);
    }
}
