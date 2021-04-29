<?php

namespace modules\offer\src\services;

use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offer\OfferRepository;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\services\OrderPriceUpdater;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\services\ProductQuoteService;

/**
 * Class OfferService
 * @package modules\offer\src\services
 *
 * @property-read ProductQuoteService $productQuoteService
 * @property-read ProductQuoteRepository $productQuoteRepository
 * @property-read OrderPriceUpdater $orderPriceUpdater
 * @property-read OfferRepository $offerRepository
 */
class OfferService
{
    private ProductQuoteService $productQuoteService;
    private ProductQuoteRepository $productQuoteRepository;
    private OrderPriceUpdater $orderPriceUpdater;
    private OfferRepository $offerRepository;

    public function __construct(
        ProductQuoteService $productQuoteService,
        ProductQuoteRepository $productQuoteRepository,
        OrderPriceUpdater $orderPriceUpdater,
        OfferRepository $offerRepository
    ) {
        $this->productQuoteService = $productQuoteService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->orderPriceUpdater = $orderPriceUpdater;
        $this->offerRepository = $offerRepository;
    }

    public function confirmAlternative(Offer $offer): OfferConfirmAlternativeResultDTO
    {
        if ($offer->isConfirm()) {
            throw new \DomainException('Offer has already be confirmed');
        }

        if (!$offer->isPending()) {
            throw new \DomainException('Offer can only be confirmed if it is in the Pending status');
        }

        $dto = new OfferConfirmAlternativeResultDTO();
        foreach ($offer->offerProducts as $offerProduct) {
            $productQuote = $offerProduct->opProductQuote;
            if (($originQuote = ProductQuoteQuery::getOriginProductQuoteByAlternative($productQuote->pq_id)) && $originQuote->pq_order_id) {
                $dto->orderId = $productQuote->pq_order_id = $originQuote->pq_order_id;
                $this->productQuoteService->detachProductQuoteFromOrder($originQuote);
                $this->productQuoteRepository->save($productQuote);
                $dto->cntConfirmedQuotes++;
            }
        }

        if ($dto->orderId) {
            $this->orderPriceUpdater->update($dto->orderId);
        }

        $offer->confirm();
        $offer->detachBehavior('user');
        $this->offerRepository->save($offer);

        return $dto;
    }
}
