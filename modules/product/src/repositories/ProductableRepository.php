<?php

namespace modules\product\src\repositories;

use modules\product\src\entities\productType\ProductType;
use modules\flight\models\Flight;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\hotel\models\Hotel;
use modules\hotel\src\repositories\hotel\HotelRepository;
use modules\product\src\exceptions\ProductCodeException;
use modules\product\src\interfaces\Productable;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\repositories\rentCar\RentCarRepository;
use yii\helpers\VarDumper;

/**
 * Class ProductableRepository
 *
 * @property FlightRepository $flightRepository
 * @property HotelRepository $hotelRepository
 * @property RentCarRepository $rentCarRepository
 */
class ProductableRepository
{
    private $flightRepository;
    private $hotelRepository;
    private $rentCarRepository;

    public function __construct(
        FlightRepository $flightRepository,
        HotelRepository $hotelRepository,
        RentCarRepository $rentCarRepository
    ) {
        $this->flightRepository = $flightRepository;
        $this->hotelRepository = $hotelRepository;
        $this->rentCarRepository = $rentCarRepository;
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
        if ($typeId === ProductType::PRODUCT_RENT_CAR) {
            /** @var RentCar $product */
            return $this->rentCarRepository->save($product);
        }
        throw new \DomainException('Invalid product type', ProductCodeException::INVALID_PRODUCT_TYPE_PRODUCTABLE_REPOSITORY);
    }
}
