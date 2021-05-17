<?php

namespace modules\offer\src\services;

use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offer\OfferRepository;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\services\OrderPriceUpdater;
use modules\product\controllers\ProductQuoteRelationCrudController;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelationQuery;
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

        $productQuoteRelations = ProductQuoteRelationQuery::getAlternativeJoinedOffer($offer->of_id);

        foreach ($productQuoteRelations as $productQuoteRelation) {
            $alternativeProductQuote = $productQuoteRelation->alternativeProductQuote;
            $originProductQuote = $productQuoteRelation->originProductQuote;

            if ($originProductQuote->pq_order_id && !$dto->orderId) {
                $dto->orderId = $originProductQuote->pq_order_id;
            }
            $this->productQuoteService->detachProductQuoteFromOrder($originProductQuote);

            $alternativeProductQuote->pq_order_id = $dto->orderId;
            $this->productQuoteRepository->save($alternativeProductQuote);
            $dto->cntConfirmedQuotes++;
        }

        if ($dto->orderId) {
            foreach ($offer->opProductQuotes as $productQuote) {
                $productQuote->pq_order_id = $dto->orderId;
                $productQuote->applied();
                $this->productQuoteRepository->save($productQuote);
            }

            $this->orderPriceUpdater->update($dto->orderId);
        }

        $offer->confirm();
        $offer->detachBehavior('user');
        $this->offerRepository->save($offer);

        if (!$dto->cntConfirmedQuotes) {
            throw new \DomainException('Offer does not contain quotes that can be confirmed');
        }

        return $dto;
    }

    public function cancelAlternative(Offer $offer): void
    {
        $offerProductQuotes = $offer->opProductQuotes;

        if ($offerProductQuotes) {
            foreach ($offerProductQuotes as $offerProductQuote) {
                if ($order = $offerProductQuote->pqOrder) {
                    $originQuote = ProductQuoteQuery::getOriginProductQuoteByAlternative($offerProductQuote->pq_id);
                    if ($originQuote) {
                        $originQuote->pq_order_id = $order->or_id;
                        $this->productQuoteRepository->save($originQuote);
                        $offerProductQuote->pq_order_id = null;
                        $offerProductQuote->pending();
                        $this->productQuoteRepository->save($offerProductQuote);
                    } else {
                        $offerProductQuote->pq_order_id = null;
                        $offerProductQuote->pending();
                        $this->productQuoteRepository->save($offerProductQuote);
                    }
                }
            }
        }

        $offer->pending();
        $this->offerRepository->save($offer);
    }
}
