<?php

namespace modules\product\src\entities\productQuoteChangeRelation;

use modules\product\src\entities\productQuote\ProductQuoteStatus;
use src\repositories\AbstractBaseRepository;

/**
 * Class ProductQuoteChangeRelationRepository
 */
class ProductQuoteChangeRelationRepository extends AbstractBaseRepository
{
    /**
     * @param ProductQuoteChangeRelation $model
     */
    public function __construct(ProductQuoteChangeRelation $model)
    {
        parent::__construct($model);
    }

    public function getModel(): ProductQuoteChangeRelation
    {
        return $this->model;
    }

    public static function exist(int $productQuoteChangeId, int $productQuoteId): bool
    {
        return ProductQuoteChangeRelation::find()
            ->where(['pqcr_pqc_id' => $productQuoteChangeId, 'pqcr_pq_id' => $productQuoteId])
            ->exists();
    }

    public static function hasAvailableProductQuotes(int $quoteChangeId, int $quoteId): bool
    {
        return ProductQuoteChangeRelation::find()
            ->innerJoinWith([
                'pqcrPqc' => function (\yii\db\ActiveQuery $query) use ($quoteChangeId) {
                    $query
                        ->andWhere(['pqc_id' => $quoteChangeId])
                    ;
                },
                'pqcrPq' => function (\yii\db\ActiveQuery $query) use ($quoteId) {
                    $query
                        ->andWhere(['<>', 'pq_id', $quoteId])
                        ->andWhere(['NOT', ['pq_status_id' => ProductQuoteStatus::CANCEL_GROUP]])
                    ;
                },
            ])
            ->exists();
    }
}
