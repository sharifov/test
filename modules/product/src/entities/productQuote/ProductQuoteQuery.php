<?php

namespace modules\product\src\entities\productQuote;

use modules\offer\src\entities\offerProduct\OfferProduct;
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

    public static function getOriginProductQuoteByReprotection(int $reprotectionQuoteId): ?ProductQuote
    {
        $query = ProductQuote::find()
            ->innerJoin(
                ProductQuoteRelation::tableName(),
                new Expression(
                    'pq_id = pqr_parent_pq_id and pqr_related_pq_id = :quoteId and pqr_type_id = :typeId',
                    ['quoteId' => $reprotectionQuoteId, 'typeId' => ProductQuoteRelation::TYPE_REPROTECTION]
                )
            );
        return $query->one();
    }

    /**
     * @param int $offerId
     * @return ProductQuote[]
     */
    public static function getOriginQuotesRelatedOffer(int $offerId): array
    {
        $query = ProductQuote::find();
        $query->innerJoin(
            ProductQuoteRelation::tableName(),
            new Expression(
                'pq_id = pqr_parent_pq_id and pqr_type_id = :typeId',
                ['typeId' => ProductQuoteRelation::TYPE_ALTERNATIVE]
            )
        );
        $query->innerJoin(OfferProduct::tableName(), 'op_offer_id = :offerId and op_product_quote_id = pq_id', ['offerId' => $offerId]);
        return $query->all();
    }

    /**
     * @param int $offerId
     * @return ProductQuote[]
     */
    public static function getAlternativeQuotesRelatedOffer(int $offerId): array
    {
        $query = ProductQuote::find();
        $query->select(['product_quote.*']);
        $query->addSelect(['product_quote_relation.pqr_parent_pq_id']);
        $query->innerJoin(
            ProductQuoteRelation::tableName(),
            new Expression(
                'pq_id = pqr_related_pq_id and pqr_type_id = :typeId',
                ['typeId' => ProductQuoteRelation::TYPE_ALTERNATIVE]
            )
        );
        $query->innerJoin(OfferProduct::tableName(), 'op_offer_id = :offerId and op_product_quote_id = pq_id', ['offerId' => $offerId]);
        return $query->all();
    }
}
