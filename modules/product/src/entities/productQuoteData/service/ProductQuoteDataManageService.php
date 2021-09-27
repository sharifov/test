<?php

namespace modules\product\src\entities\productQuoteData\service;

use modules\product\src\entities\productQuoteData\ProductQuoteData;
use modules\product\src\entities\productQuoteData\ProductQuoteDataQuery;

class ProductQuoteDataManageService
{
    public function updateRecommendedReprotectionQuote(int $originProductQuoteId, int $reprotectionQuoteId): ProductQuoteData
    {
        ProductQuoteDataQuery::removeRecommendedByOriginQuote($originProductQuoteId);

        $recommendedQuote = ProductQuoteData::createRecommended($reprotectionQuoteId);
        if (!$recommendedQuote->save()) {
            throw new \RuntimeException('Unable to set recommended reprotection quote flag: ' . $recommendedQuote->getErrorSummary(true)[0]);
        }
        return $recommendedQuote;
    }
}
