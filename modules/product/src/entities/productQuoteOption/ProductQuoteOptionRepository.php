<?php

namespace modules\product\src\entities\productQuoteOption;

use modules\product\src\exceptions\ProductCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class ProductQuoteOptionRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class ProductQuoteOptionRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): ProductQuoteOption
    {
        if ($productQuoteOption = ProductQuoteOption::findOne($id)) {
            return $productQuoteOption;
        }
        throw new NotFoundException('Product Quote Option is not found', ProductCodeException::PRODUCT_QUOTE_OPTION_NOT_FOUND);
    }

    public function save(ProductQuoteOption $productQuoteOption): int
    {
        if (!$productQuoteOption->save(false)) {
            throw new \RuntimeException('Saving error', ProductCodeException::PRODUCT_QUOTE_OPTION_SAVE);
        }
        $this->eventDispatcher->dispatchAll($productQuoteOption->releaseEvents());
        return $productQuoteOption->pqo_id;
    }

    public function remove(ProductQuoteOption $productQuoteOption): void
    {
        if (!$productQuoteOption->delete()) {
            throw new \RuntimeException('Removing error', ProductCodeException::PRODUCT_QUOTE_OPTION_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($productQuoteOption->releaseEvents());
    }
}
