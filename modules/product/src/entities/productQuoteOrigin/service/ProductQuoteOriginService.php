<?php

namespace modules\product\src\entities\productQuoteOrigin\service;

use modules\product\src\entities\productQuoteOrigin\ProductQuoteOrigin;
use modules\product\src\entities\productQuoteOrigin\repository\ProductQuoteOriginRepository;

/**
 * Class ProductQuoteOriginService
 *
 * @property-read ProductQuoteOriginRepository $repository
 */
class ProductQuoteOriginService
{
    /**
     * @var ProductQuoteOriginRepository
     */
    private ProductQuoteOriginRepository $repository;

    public function __construct(ProductQuoteOriginRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(int $productId, int $quoteId): ProductQuoteOrigin
    {
        $productQuoteOrigin = ProductQuoteOrigin::create($productId, $quoteId);
        $this->repository->save($productQuoteOrigin);
        return $productQuoteOrigin;
    }
}
