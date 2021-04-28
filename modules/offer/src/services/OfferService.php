<?php

namespace modules\offer\src\services;

use modules\offer\src\entities\offer\Offer;
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
 */
class OfferService
{
    private ProductQuoteService $productQuoteService;
    private ProductQuoteRepository $productQuoteRepository;
    private OrderPriceUpdater $orderPriceUpdater;

    public function __construct(
        ProductQuoteService $productQuoteService,
        ProductQuoteRepository $productQuoteRepository,
        OrderPriceUpdater $orderPriceUpdater
    ) {
        $this->productQuoteService = $productQuoteService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->orderPriceUpdater = $orderPriceUpdater;
    }

    public function confirmAlternative(Offer $offer): void
    {
        $order = null;
        foreach ($offer->offerProducts as $offerProduct) {
            $productQuote = $offerProduct->opProductQuote;
            if (($originQuote = ProductQuoteQuery::getOriginProductQuoteByAlternative($productQuote->pq_id)) && $originQuote->pq_order_id) {
                $productQuote->pq_order_id = $originQuote->pq_order_id;
                $this->productQuoteService->detachProductQuoteFromOrder($originQuote);
                $this->productQuoteRepository->save($productQuote);
            }
        }

        /** @var Order|null $order */
        if ($order) {
            $this->orderPriceUpdater->update($order->or_id);
        }
    }
}
