<?php

namespace modules\product\src\entities\productQuoteRelation;

use modules\offer\src\entities\offerProduct\OfferProduct;

class ProductQuoteRelationQuery
{
    public static function isRelatedAlternativeQuoteExists(int $relatedQuoteId): bool
    {
        $query = ProductQuoteRelation::find();
        return $query->byRelatedQuoteId($relatedQuoteId)->alternative()->exists();
    }

    public static function getAlternativeQuoteIdsByOrigin(int $originQuoteId): array
    {
        $query = ProductQuoteRelation::find()->select('pqr_related_pq_id');
        $query->byParentQuoteId($originQuoteId)
            ->alternative();
        return $query->asArray()->column();
    }

    public static function isOriginQuoteExists(int $originQuoteId): bool
    {
        $query = ProductQuoteRelation::find()->select('pqr_related_pq_id');
        $query->byParentQuoteId($originQuoteId)
            ->alternative();
        return $query->exists();
    }

    /**
     * @param int $offerId
     * @return ProductQuoteRelation[]
     */
    public static function getAlternativeJoinedOffer(int $offerId): array
    {
        $query = ProductQuoteRelation::find();
        $query->innerJoin(OfferProduct::tableName(), 'op_offer_id = :offerId and op_product_quote_id = pqr_related_pq_id', [
            'offerId' => $offerId
        ]);
        $query->andWhere(['pqr_type_id' => ProductQuoteRelation::TYPE_ALTERNATIVE]);
        return $query->all();
    }

    public static function getReProtectionQuotesIds(int $originQuoteId): array
    {
        return ProductQuoteRelation::find()
            ->select('pqr_related_pq_id')
            ->byParentQuoteId($originQuoteId)
            ->reprotection()
            ->asArray()
            ->column();
    }

    public static function countReprotectionQuotesByOrigin(int $originQuoteId): int
    {
        return (int)ProductQuoteRelation::find()->byParentQuoteId($originQuoteId)->reprotection()->count();
    }

    public static function countVoluntaryExchangeByOrigin(int $originQuoteId): int
    {
        return (int) ProductQuoteRelation::find()->byParentQuoteId($originQuoteId)->voluntaryExchange()->count();
    }
}
