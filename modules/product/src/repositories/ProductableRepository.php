<?php

namespace modules\product\src\repositories;

use modules\product\src\entities\productType\ProductType;
use modules\flight\models\Flight;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\hotel\models\Hotel;
use modules\hotel\src\repositories\hotel\HotelRepository;
use modules\product\src\exceptions\ProductCodeException;
use modules\product\src\interfaces\Productable;
use yii\helpers\VarDumper;

/**
 * Class ProductableRepository
 *
 * @property FlightRepository $flightRepository
 * @property HotelRepository $hotelRepository
 */
class ProductableRepository
{
    private $flightRepository;
    private $hotelRepository;

    public function __construct(
        FlightRepository $flightRepository,
        HotelRepository $hotelRepository
    )
    {
        $this->flightRepository = $flightRepository;
        $this->hotelRepository = $hotelRepository;
    }

    public function save(int $typeId, Productable $product): int
    {
        if ($typeId === ProductType::PRODUCT_FLIGHT) {
            /** @var Flight $product */
            return $this->flightRepository->save($product);
        }
        if ($typeId === ProductType::PRODUCT_HOTEL) {
            /** @var Hotel $product */
            return $this->hotelRepository->save($product);
        }
        throw new \DomainException('Invalid product type', ProductCodeException::INVALID_PRODUCT_TYPE_PRODUCTABLE_REPOSITORY);
    }
}
