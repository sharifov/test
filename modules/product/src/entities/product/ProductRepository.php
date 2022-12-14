<?php

namespace modules\product\src\entities\product;

use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\exceptions\ProductCodeException;
use src\dispatchers\EventDispatcher;
use src\repositories\NotFoundException;

/**
 * Class ProductRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class ProductRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): Product
    {
        if ($product = Product::findOne($id)) {
            return $product;
        }
        throw new NotFoundException('Product is not found', ProductCodeException::PRODUCT_NOT_FOUND);
    }

    public function save(Product $product): int
    {
        if (!$product->save(false)) {
            throw new \RuntimeException('Saving error', ProductCodeException::PRODUCT_SAVE);
        }
        $this->eventDispatcher->dispatchAll($product->releaseEvents());
        return $product->pr_id;
    }

    public function remove(Product $product): void
    {
        if (!$product->isDeletable()) {
            throw new \RuntimeException(
                'The product cannot be removed. Exist quotes is not deletable statuses (' .
                ProductQuoteStatus::getNotDeletableStatusGroupNames() . ')',
                ProductCodeException::PRODUCT_REMOVE
            );
        }

        if (!$product->delete()) {
            throw new \RuntimeException('Removing error', ProductCodeException::PRODUCT_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($product->releaseEvents());
    }
}
