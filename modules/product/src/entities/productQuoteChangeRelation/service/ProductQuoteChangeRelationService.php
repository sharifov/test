<?php

namespace modules\product\src\entities\productQuoteChangeRelation\service;

use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelationRepository;

/**
 * Class ProductQuoteChangeRelationService
 */
class ProductQuoteChangeRelationService
{
    public static function getOrCreate(int $productQuoteChangeId, int $productQuoteId): ProductQuoteChangeRelation
    {
        if ($model = self::getModel($productQuoteChangeId, $productQuoteId)) {
            return $model;
        }
        $model = ProductQuoteChangeRelation::create(
            $productQuoteChangeId,
            $productQuoteId
        );
        (new ProductQuoteChangeRelationRepository($model))->save();
        return $model;
    }

    public static function getModel(int $productQuoteChangeId, int $productQuoteId): ?ProductQuoteChangeRelation
    {
        return ProductQuoteChangeRelation::find()
            ->where(['pqcr_pqc_id' => $productQuoteChangeId, 'pqcr_pq_id' => $productQuoteId])
            ->one();
    }
}
