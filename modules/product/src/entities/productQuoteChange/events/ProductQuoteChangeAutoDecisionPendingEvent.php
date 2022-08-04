<?php

namespace modules\product\src\entities\productQuoteChange\events;

/**
 * Class ProductQuoteChangeAutoDecisionPendingEvent
 *
 * @property int $productQuoteChangeId
 */
class ProductQuoteChangeAutoDecisionPendingEvent implements ProductQuoteChangeInterface
{
    public int $productQuoteChangeId;

    public function __construct(int $productQuoteChangeId)
    {
        $this->productQuoteChangeId = $productQuoteChangeId;
    }

    public function getProductQuoteChangeId(): int
    {
        return $this->productQuoteChangeId;
    }

    public function getClass(): string
    {
        return self::class;
    }
}
