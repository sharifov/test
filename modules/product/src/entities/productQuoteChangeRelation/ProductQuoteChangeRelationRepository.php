<?php

namespace modules\product\src\entities\productQuoteChangeRelation;

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
}
