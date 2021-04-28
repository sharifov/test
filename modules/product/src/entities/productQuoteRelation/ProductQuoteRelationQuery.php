<?php

namespace modules\product\src\entities\productQuoteRelation;

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
}
