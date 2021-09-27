<?php

namespace modules\product\src\entities\productQuoteData;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;

class ProductQuoteDataQuery
{
    public static function removeRecommendedByOriginQuote(int $id): int
    {
        $reprotectionQuotesQuery = ProductQuote::find()->select(['pq_id'])
            ->with('productQuoteDataRecommended')
            ->innerJoin(ProductQuoteRelation::tableName(), 'pqr_related_pq_id = pq_id and pqr_parent_pq_id = :parentQuoteId and pqr_type_id = :typeId', [
                'typeId' => ProductQuoteRelation::TYPE_REPROTECTION,
                'parentQuoteId' => $id
            ]);

        return ProductQuoteData::deleteAll([
            'pqd_key' => ProductQuoteDataKey::RECOMMENDED,
            'pqd_product_quote_id' => $reprotectionQuotesQuery
        ]);
    }
}
