<?php

namespace modules\product\src\services\productQuote;

use modules\flight\models\FlightQuote;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\services\flightQuote\FlightQuoteCloneService;
use modules\hotel\src\services\hotelQuote\HotelQuoteCloneService;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionRepository;
use sales\services\TransactionManager;

/**
 * Class ProductQuoteReplaceService
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property ProductQuoteOptionRepository $productQuoteOptionRepository
 * @property HotelQuoteCloneService $hotelQuoteCloneService
 * @property ProductRepository $productRepository
 * @property FlightQuoteCloneService $flightQuoteCloneService
 * @property FlightQuoteRepository $flightQuoteRepository
 */
class ProductQuoteReplaceService
{
    private ProductQuoteRepository $productQuoteRepository;
    private TransactionManager $transactionManager;
    private ProductQuoteOptionRepository $productQuoteOptionRepository;
    private HotelQuoteCloneService $hotelQuoteCloneService;
    private ProductRepository $productRepository;
    private FlightQuoteCloneService $flightQuoteCloneService;
    private FlightQuoteRepository $flightQuoteRepository;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        ProductQuoteOptionRepository $productQuoteOptionRepository,
        TransactionManager $transactionManager,
        HotelQuoteCloneService $hotelQuoteCloneService,
        ProductRepository $productRepository,
        FlightQuoteCloneService $flightQuoteCloneService,
        FlightQuoteRepository $flightQuoteRepository
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->productQuoteOptionRepository = $productQuoteOptionRepository;
        $this->hotelQuoteCloneService = $hotelQuoteCloneService;
        $this->productRepository = $productRepository;
        $this->flightQuoteCloneService = $flightQuoteCloneService;
        $this->flightQuoteRepository = $flightQuoteRepository;
    }

    public function replaceFromApiBo(int $productQuoteId, FlightQuote $originFlightQuote): ProductQuote
    {
        $originalQuote = $this->productQuoteRepository->find($productQuoteId);

        return $this->transactionManager->wrap(function () use ($originalQuote, $originFlightQuote) {
            $productQuote = ProductQuote::replace($originalQuote);
            $this->productQuoteRepository->save($productQuote);

            $childQuote = $originalQuote->getChildQuote();
            $childProduct = $originalQuote->pqProduct->getChildProduct();

            if ($childQuote && $childProduct) {
                if ($originalQuote->isHotel()) {
                    $this->hotelQuoteCloneService->clone($childQuote->getId(), $childProduct->getId(), $productQuote->pq_id);
                } elseif ($originalQuote->isFlight()) {
                    $quote = FlightQuote::clone($originFlightQuote, $originFlightQuote->fq_flight_id, $productQuote->pq_id);
                    $this->flightQuoteRepository->save($quote);
                } else {
                    throw new \DomainException('Undefined Product Quote type');
                }
            }

            $originalQuote->cancelled(null, 'Cancelled from point - ReplaceFromApiBo. New QuoteId(' . $productQuote->pq_id . ')');
            $this->productQuoteRepository->save($originalQuote);

            return $productQuote;
        });
    }
}
