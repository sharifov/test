<?php

namespace modules\product\src\entities\productQuote;

use modules\product\src\exceptions\ProductCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

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
        if (!$productQuote->delete()) {
            throw new \RuntimeException('Removing error', ProductCodeException::PRODUCT_QUOTE_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($productQuote->releaseEvents());
    }
}
