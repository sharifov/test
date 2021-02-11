<?php

namespace modules\product\src\services;

use modules\cruise\src\entity\cruise\Cruise;
use modules\product\src\entities\productType\ProductType;
use modules\flight\models\Flight;
use modules\hotel\models\Hotel;
use modules\product\src\exceptions\ProductCodeException;
use modules\product\src\interfaces\Productable;
use modules\rentCar\src\entity\rentCar\RentCar;

class ProductFactory
{
    public function create(int $typeId, int $productId): Productable
    {
        switch ($typeId) {
            case ProductType::PRODUCT_FLIGHT:
                return Flight::create($productId);
            case ProductType::PRODUCT_HOTEL:
                return Hotel::create($productId);
            case ProductType::PRODUCT_RENT_CAR:
                return RentCar::create($productId);
            case ProductType::PRODUCT_CRUISE:
                return Cruise::create($productId);
        }
        throw new \DomainException('Invalid product type', ProductCodeException::INVALID_PRODUCT_TYPE_FACTORY);
    }
}
