<?php

namespace modules\product\src\entities\productQuoteData\service;

use modules\product\src\entities\productQuoteData\ProductQuoteData;
use modules\product\src\entities\productQuoteData\ProductQuoteDataQuery;

/**
 * Class ProductQuoteDataManageService
 */
class ProductQuoteDataManageService
{
    public function updateRecommendedChangeQuote(int $originProductQuoteId, int $changeQuoteId): ProductQuoteData
    {
        ProductQuoteDataQuery::removeRecommendedByOriginQuote($originProductQuoteId);

        $recommendedQuote = ProductQuoteData::createRecommended($changeQuoteId);
        if (!$recommendedQuote->save()) {
            throw new \RuntimeException('Unable to set change quote flag: ' . $recommendedQuote->getErrorSummary(true)[0]);
        }
        return $recommendedQuote;
    }
}
