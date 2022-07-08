<?php

namespace modules\product\src\entities\productQuote;

use modules\product\src\entities\product\Product;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\exceptions\ProductCodeException;
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

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): ProductQuote
    {
        if ($productQuote = ProductQuote::findOne($id)) {
            return $productQuote;
        }
        throw new NotFoundException('Product Quote is not found', ProductCodeException::PRODUCT_QUOTE_NOT_FOUND);
    }

    public function save(ProductQuote $productQuote): int
    {
        if (!$productQuote->save(false)) {
            throw new \RuntimeException('Saving error', ProductCodeException::PRODUCT_QUOTE_SAVE);
        }
        $this->eventDispatcher->dispatchAll($productQuote->releaseEvents());
        return $productQuote->pq_id;
    }

    public function remove(ProductQuote $productQuote): void
    {
        if (!$productQuote->isDeletable()) {
            throw new \RuntimeException(
                'ProductQuote cannot be removed. Status is not deletable . (' . ProductQuoteStatus::getNotDeletableStatusGroupNames() . ')',
                ProductCodeException::PRODUCT_QUOTE_REMOVE
            );
        }

        if (!$productQuote->delete()) {
            throw new \RuntimeException('Removing error', ProductCodeException::PRODUCT_QUOTE_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($productQuote->releaseEvents());
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

    public static function updateProductQuoteStatusByBOSaleStatus($originalProductQuote, $saleData)
    {
        if (!empty($originalProductQuote) && isset($saleData['saleStatus']) && is_string($saleData['saleStatus'])) {
            $saleStatusBoMap = ProductQuoteStatus::STATUS_BO_MAP[strtolower($saleData['saleStatus'])] ?? null;
            if (!empty($saleStatusBoMap) && $originalProductQuote->pq_status_id !== $saleStatusBoMap) {
                $originalProductQuote->pq_status_id = $saleStatusBoMap;
                $originalProductQuote->save();
            }
        }
    }
}
