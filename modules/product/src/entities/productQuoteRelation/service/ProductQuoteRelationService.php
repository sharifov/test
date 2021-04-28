<?php

namespace modules\product\src\entities\productQuoteRelation\service;

use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\repositories\ProductQuoteRelationRepository;

/**
 * Class ProductQuoteRelationService
 *
 * @property-read ProductQuoteRelationRepository $productQuoteRelationRepository
 */
class ProductQuoteRelationService
{
    private ProductQuoteRelationRepository $productQuoteRelationRepository;

    public function __construct(ProductQuoteRelationRepository $productQuoteRelationRepository)
    {
        $this->productQuoteRelationRepository = $productQuoteRelationRepository;
    }

    public function createAlternative(int $parentQuoteId, $relatedQuoteId, int $createdUserId): ProductQuoteRelation
    {
        $relation = ProductQuoteRelation::createAlternative($parentQuoteId, $relatedQuoteId, $createdUserId);
        $this->productQuoteRelationRepository->save($relation);
        return $relation;
    }
}
