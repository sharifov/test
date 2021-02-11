<?php

namespace modules\product\src\repositories;

use modules\product\src\entities\productType\ProductType;
use modules\flight\models\Flight;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\attraction\src\repositories\attraction\AttractionRepository;
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
 * @property AttractionRepository $attractionRepository
 */
class ProductableRepository
{
    private $flightRepository;
    private $hotelRepository;
    private $attractionRepository;

    public function __construct(
        FlightRepository $flightRepository,
        HotelRepository $hotelRepository,
        AttractionRepository $attractionRepository
    ) {
        $this->flightRepository = $flightRepository;
        $this->hotelRepository = $hotelRepository;
        $this->attractionRepository = $attractionRepository;
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
        if ($typeId === ProductType::PRODUCT_ATTRACTION) {
            /** @var Hotel $product */
            return $this->attractionRepository->save($product);
        }
        throw new \DomainException('Invalid product type', ProductCodeException::INVALID_PRODUCT_TYPE_PRODUCTABLE_REPOSITORY);
    }
}
