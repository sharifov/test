<?php

namespace modules\product\src\entities\productQuoteRelation;

class ProductQuoteRelationQuery
{
    public static function isRelatedAlternativeQuoteExists(int $relatedQuoteId): bool
    {
        $query = ProductQuoteRelation::find();
        return $query->byRelatedQuoteId($relatedQuoteId)->alternative()->exists();
    }

    public static function getRelatedAlternativeQuote(int $relatedQuoteId): bool
    {
        $query = ProductQuoteRelation::find();
        $query->byRelatedQuoteId($relatedQuoteId)
            ->alternative()
            ->innerJoin('');
    }
}
