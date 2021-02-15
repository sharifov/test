<?php

namespace modules\hotel\src\useCases\request\update;

use modules\hotel\src\repositories\hotel\HotelRepository;
use modules\hotel\src\useCases\api\searchQuote\HotelQuoteSearchService;
use modules\product\src\entities\product\ProductRepository;

/**
 * Class HotelRequestUpdateService
 *
 * @property HotelRepository $hotelRepository
 * @property HotelQuoteSearchService $hotelQuoteSearchService
 */
class HotelRequestUpdateService
{
    private $hotelRepository;

    private $hotelQuoteSearchService;

    public function __construct(HotelRepository $hotelRepository, HotelQuoteSearchService $hotelQuoteSearchService)
    {
        $this->hotelRepository = $hotelRepository;
        $this->hotelQuoteSearchService = $hotelQuoteSearchService;
    }

    public function update(HotelUpdateRequestForm $form): void
    {
        $hotel = $this->hotelRepository->find($form->getHotelId());
        $hotel->updateRequest($form);
        $this->hotelRepository->save($hotel);
        $this->hotelQuoteSearchService->clearCache($hotel->ph_request_hash_key);
    }
}
