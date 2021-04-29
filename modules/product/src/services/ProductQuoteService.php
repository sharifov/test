<?php

namespace modules\product\src\services;

use common\models\Currency;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;

/**
 * Class ProductQuoteService
 * @package modules\product\src\services
 *
 * @property ProductQuoteRepository $productQuoteRepository
 */
class ProductQuoteService
{
    /**
     * @var ProductQuoteRepository
     */
    private $productQuoteRepository;

    /**
     * ProductQuoteService constructor.
     * @param ProductQuoteRepository $productQuoteRepository
     */
    public function __construct(ProductQuoteRepository $productQuoteRepository)
    {
        $this->productQuoteRepository = $productQuoteRepository;
    }

    /**
     * @param ProductQuote $productQuote
     * @param Currency $clientCurrency
     */
    public function recountProductQuoteClientPrice(ProductQuote $productQuote, Currency $clientCurrency): void
    {
        $productQuote->recountClientPrice($clientCurrency);
        $this->productQuoteRepository->save($productQuote);
    }

    public function detachProductQuoteFromOrder(ProductQuote $productQuote): void
    {
        if ($productQuote->isInProgress() || $productQuote->isPending()) {
            $productQuote->declined();
        }
        $productQuote->pq_order_id = null;
        $this->productQuoteRepository->save($productQuote);
    }
}
