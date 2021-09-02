<?php

namespace modules\product\src\entities\productQuoteData;

class ProductQuoteDataRepository
{
    public function save(ProductQuoteData $productQuoteData): int
    {
        if (!$productQuoteData->save()) {
            throw new \RuntimeException('Product Quote Data saving failed: ' . $productQuoteData->getErrorSummary(true)[0]);
        }
        return $productQuoteData->pqd_id;
    }
}
