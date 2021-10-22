<?php

namespace modules\product\src\entities\productQuoteData;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;

/**
 * Class ProductQuoteDataQuery
 */
class ProductQuoteDataQuery
{
    public static function removeRecommendedByOriginQuote(
        int $id,
        array $typeIds = [ProductQuoteRelation::TYPE_REPROTECTION, ProductQuoteRelation::TYPE_VOLUNTARY_EXCHANGE]
    ): int {
        return ProductQuoteData::deleteAll([
            'pqd_key' => ProductQuoteDataKey::RECOMMENDED,
            'pqd_product_quote_id' => ProductQuote::find()->select(['pq_id'])
                ->with('productQuoteDataRecommended')
                ->innerJoin(ProductQuoteRelation::tableName(), 'pqr_related_pq_id = pq_id and pqr_parent_pq_id = :parentQuoteId', [
                    'parentQuoteId' => $id
                ])
                ->andWhere(['IN', 'pqr_type_id', $typeIds])
        ]);
    }
}
