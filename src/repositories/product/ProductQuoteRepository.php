<?php

namespace src\repositories\product;

use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productType\ProductType;
use src\dispatchers\EventDispatcher;
use src\repositories\NotFoundException;

/**
 * Class ProductQuoteRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class ProductQuoteRepository
{
    private $eventDispatcher;

    /**
     * CasesRepository constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
    /**
     * @param ProductQuote $productQuote
     * @return int
     */
    public function save(ProductQuote $productQuote): int
    {
        if (!$productQuote->save()) {
            throw new \RuntimeException($productQuote->getErrorSummary(false)[0]);
        }
        $this->eventDispatcher->dispatchAll($productQuote->releaseEvents());
        return $productQuote->pq_id;
    }

    public function find(int $productQuoteId): ProductQuote
    {
        if ($productQuote = ProductQuote::findOne($productQuoteId)) {
            return $productQuote;
        }
        throw new NotFoundException('Product Quote not found');
    }

    public function findByGidFlightProductQuote(string $gid): ProductQuote
    {
        $productQuote = ProductQuote::find()
            ->where(['pq_gid' => $gid])
            ->innerJoin(
                Product::tableName(),
                'pq_product_id = pr_id and pr_type_id = :flightProductTypeId',
                ['flightProductTypeId' => ProductType::PRODUCT_FLIGHT]
            )
            ->one();
        if ($productQuote) {
            return $productQuote;
        }
        throw new NotFoundException('Product Quote not found');
    }
}
