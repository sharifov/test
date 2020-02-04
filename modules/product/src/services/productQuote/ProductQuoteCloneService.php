<?php

namespace modules\product\src\services\productQuote;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class ProductQuoteCloneService
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 */
class ProductQuoteCloneService
{
    private $productQuoteRepository;
    private $transactionManager;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        TransactionManager $transactionManager
    )
    {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
    }

    public function clone(int $productQuoteId, int $productId, ?int $ownerId): ProductQuote
    {
        $originalQuote = $this->productQuoteRepository->find($productQuoteId);

        $clone = $this->transactionManager->wrap(function () use ($originalQuote, $productId, $ownerId) {
            $clone = ProductQuote::clone($originalQuote, $productId, $ownerId);
            $this->productQuoteRepository->save($clone);

            return $clone;
        });

        return $clone;
    }
}
