<?php

namespace modules\offer\src\entities\offerProduct;

use modules\offer\src\exceptions\OfferCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class OfferProductRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class OfferProductRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $offerId, int $productQuoteId): OfferProduct
    {
        if ($offerProduct = OfferProduct::findOne(['op_offer_id' => $offerId, 'op_product_quote_id' => $productQuoteId])) {
            return $offerProduct;
        }
        throw new NotFoundException('Offer Product is not found', OfferCodeException::OFFER_PRODUCT_NOT_FOUND);
    }

    public function save(OfferProduct $offerProduct): void
    {
        if (!$offerProduct->save(false)) {
            throw new \RuntimeException('Saving error', OfferCodeException::OFFER_PRODUCT_SAVE);
        }
        $this->eventDispatcher->dispatchAll($offerProduct->releaseEvents());
    }

    public function remove(OfferProduct $offerProduct): void
    {
        if (!$offerProduct->delete()) {
            throw new \RuntimeException('Removing error', OfferCodeException::OFFER_PRODUCT_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($offerProduct->releaseEvents());
    }
}
