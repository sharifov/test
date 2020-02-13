<?php

namespace modules\product\src\entities\productOption;

use modules\product\src\exceptions\ProductCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class ProductOptionRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class ProductOptionRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): ProductOption
    {
        if ($productOption = ProductOption::findOne($id)) {
            return $productOption;
        }
        throw new NotFoundException('Product Option is not found', ProductCodeException::PRODUCT_OPTION_NOT_FOUND);
    }

    public function save(ProductOption $productOption): int
    {
        if (!$productOption->save(false)) {
            throw new \RuntimeException('Saving error', ProductCodeException::PRODUCT_OPTION_SAVE);
        }
        $this->eventDispatcher->dispatchAll($productOption->releaseEvents());
        return $productOption->po_id;
    }

    public function remove(ProductOption $productOption): void
    {
        if (!$productOption->delete()) {
            throw new \RuntimeException('Removing error', ProductCodeException::PRODUCT_OPTION_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($productOption->releaseEvents());
    }
}
