<?php

namespace modules\product\src\services;

use modules\product\src\entities\productQuote\ProductQuoteRepository;

/**
 * Class ProductQuoteStatusLogService
 *
 * @property ProductQuoteRepository $repository
 */
class RecalculateProfitAmountService
{
    private $repository;

    /**
     * RecalculateProfitAmountService constructor.
     * @param ProductQuoteRepository $repository
     */
    public function __construct(ProductQuoteRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $productQuoteId
     */
    public function recalculateByProductQuote(int $productQuoteId): void
    {
        $productQuote = $this->repository->find($productQuoteId);
        $productQuote->setProfitAmount();
        $this->repository->save($productQuote);
    }
}
