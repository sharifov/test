<?php

namespace modules\product\src\entities\productQuote;

use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use yii\db\Expression;

class ProductQuoteQuery
{
    public static function getOriginProductQuoteByAlternative(int $alternativeQuoteId): ?ProductQuote
    {
        $query = ProductQuote::find()
            ->innerJoin(
                ProductQuoteRelation::tableName(),
                new Expression(
                    'pq_id = pqr_parent_pq_id and pqr_related_pq_id = :quoteId and pqr_type_id = :typeId',
                    ['quoteId' => $alternativeQuoteId, 'typeId' => ProductQuoteRelation::TYPE_ALTERNATIVE]
                )
            );
        return $query->one();
    }
}
