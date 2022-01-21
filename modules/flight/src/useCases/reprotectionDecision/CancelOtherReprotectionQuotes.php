<?php

namespace modules\flight\src\useCases\reprotectionDecision;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use src\repositories\product\ProductQuoteRepository;

/**
 * Class CancelOtherReprotectionQuotes
 *
 * @property ProductQuoteRepository $productQuoteRepository
 */
class CancelOtherReprotectionQuotes
{
    private ProductQuoteRepository $productQuoteRepository;

    public function __construct(ProductQuoteRepository $productQuoteRepository)
    {
        $this->productQuoteRepository = $productQuoteRepository;
    }

    public function cancel(ProductQuote $quote, ?int $userId): void
    {
        $parentRelatedId = ProductQuoteRelation::find()->select(['pqr_parent_pq_id'])->byRelatedQuoteId($quote->pq_id)->scalar();
        if (!$parentRelatedId) {
            return;
        }

        $quotes = ProductQuote::find()
            ->andWhere([
                'pq_id' => ProductQuoteRelation::find()
                    ->select(['pqr_related_pq_id'])
                    ->byParentQuoteId((int)$parentRelatedId)
                    ->reprotection()
            ])
            ->all();

        foreach ($quotes as $productQuote) {
            if (!$productQuote->isEqual($quote) && !$productQuote->isCanceled() && $productQuote->isFlight() && $productQuote->flightQuote->isTypeReProtection()) {
                $productQuote->cancelled($userId, 'Canceled from reProtection');
                $this->productQuoteRepository->save($productQuote);
            }
        }
    }

    public function cancelByQuoteChange(ProductQuoteChange $productQuoteChange, ProductQuote $exceptQuote, ?int $userId): void
    {
        $quotes = ProductQuoteQuery::getProductQuotesByChangeQuote($productQuoteChange->pqc_id);

        foreach ($quotes as $productQuote) {
            if (!$productQuote->isEqual($exceptQuote) && !$productQuote->isCanceled() && $productQuote->isFlight() && $productQuote->flightQuote->isTypeReProtection()) {
                $productQuote->cancelled($userId, 'Canceled from reProtection');
                $this->productQuoteRepository->save($productQuote);
            }
        }
    }
}
