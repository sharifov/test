<?php

namespace modules\product\src\guards;

use modules\cruise\CruiseModule;
use modules\product\src\entities\productType\ProductType;
use modules\flight\FlightModule;
use modules\hotel\HotelModule;
use modules\attraction\AttractionModule;
use modules\product\src\exceptions\ProductCodeException;
use modules\rentCar\RentCarModule;

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

        if ($productTypeId === ProductType::PRODUCT_ATTRACTION) {
            if (!class_exists(AttractionModule::class)) {
                throw new \DomainException('Product Attraction is unavailable', ProductCodeException::PRODUCT_ATTRACTION_UNAVAILABLE);
            }
            return;
        }

        if ($productTypeId === ProductType::PRODUCT_RENT_CAR) {
            if (!class_exists(RentCarModule::class)) {
                throw new \DomainException('Product Rent Car is unavailable', ProductCodeException::PRODUCT_RENT_CAR_UNAVAILABLE);
            }
            return;
        }
        if ($productTypeId === ProductType::PRODUCT_CRUISE) {
            if (!class_exists(CruiseModule::class)) {
                throw new \DomainException('Product Cruise is unavailable', ProductCodeException::PRODUCT_CRUISE_UNAVAILABLE);
            }
            return;
        }
        throw new \DomainException('Invalid product type', ProductCodeException::INVALID_PRODUCT_TYPE_GUARD);
    }
}
