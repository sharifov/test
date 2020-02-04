<?php

namespace modules\product\src\services\productQuote;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionRepository;
use sales\services\TransactionManager;

/**
 * Class ProductQuoteCloneService
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property ProductQuoteOptionRepository $productQuoteOptionRepository
 */
class ProductQuoteCloneService
{
    private $productQuoteRepository;
    private $transactionManager;
    private $productQuoteOptionRepository;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        ProductQuoteOptionRepository $productQuoteOptionRepository,
        TransactionManager $transactionManager
    )
    {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->productQuoteOptionRepository = $productQuoteOptionRepository;
    }

    public function clone(int $productQuoteId, int $productId, ?int $ownerId): ProductQuote
    {
        $originalQuote = $this->productQuoteRepository->find($productQuoteId);

        $clone = $this->transactionManager->wrap(function () use ($originalQuote, $productId, $ownerId) {

            $productQuote = ProductQuote::clone($originalQuote, $productId, $ownerId);
            $this->productQuoteRepository->save($productQuote);

            foreach ($originalQuote->productQuoteOptions as $originalProductQuoteOption) {
                $productQuoteOption = ProductQuoteOption::clone($originalProductQuoteOption, $productQuote->pq_id);
                $this->productQuoteOptionRepository->save($productQuoteOption);
            }

            return $productQuote;
        });

        return $clone;
    }
}
