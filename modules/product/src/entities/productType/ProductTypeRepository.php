<?php

namespace modules\product\src\entities\productType;

use modules\product\src\exceptions\ProductCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class ProductTypeRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class ProductTypeRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): ProductType
    {
        if ($productType = ProductType::findOne($id)) {
            return $productType;
        }
        throw new NotFoundException('Product Type is not found', ProductCodeException::PRODUCT_TYPE_NOT_FOUND);
    }

    public function save(ProductType $productType): int
    {
        if (!$productType->save(false)) {
            throw new \RuntimeException('Saving error', ProductCodeException::PRODUCT_TYPE_SAVE);
        }
        $this->eventDispatcher->dispatchAll($productType->releaseEvents());
        return $productType->pt_id;
    }

    public function remove(ProductType $productType): void
    {
        if (!$productType->delete()) {
            throw new \RuntimeException('Removing error', ProductCodeException::PRODUCT_TYPE_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($productType->releaseEvents());
    }
}
