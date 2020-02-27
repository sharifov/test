<?php

namespace modules\hotel\src\useCases\request\update;

use modules\hotel\src\repositories\hotel\HotelRepository;
use modules\product\src\entities\product\ProductRepository;

/**
 * Class HotelRequestUpdateService
 *
 * @property ProductRepository $productRepository
 */
class HotelRequestUpdateService
{
    private $hotelRepository;

    public function __construct(HotelRepository $hotelRepository)
    {
        $this->hotelRepository = $hotelRepository;
    }

    public function update(HotelUpdateRequestForm $form): void
    {
        $hotel = $this->hotelRepository->find($form->getHotelId());
        $hotel->updateRequest($form);
        $this->hotelRepository->save($hotel);
    }
}
