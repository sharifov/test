<?php

namespace modules\hotel\src\services\hotelQuote;

use modules\hotel\src\entities\hotelQuote\HotelQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class HotelQuoteCloneService
 *
 * @property TransactionManager $transactionManager
 * @property HotelQuoteRepository $hotelQuoteRepository
 */
class HotelQuoteCloneService
{
    private $transactionManager;
    private $hotelQuoteRepository;

    public function __construct(
        TransactionManager $transactionManager,
        HotelQuoteRepository $hotelQuoteRepository
    )
    {
        $this->transactionManager = $transactionManager;
        $this->hotelQuoteRepository = $hotelQuoteRepository;
    }

    public function clone(int $originalQuoteId)
    {
        $originalQuote = $this->hotelQuoteRepository->find($originalQuoteId);

    }
}
