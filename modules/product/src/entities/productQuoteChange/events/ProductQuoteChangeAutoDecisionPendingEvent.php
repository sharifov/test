<?php

namespace modules\product\src\entities\productQuoteChange\events;

/**
 * Class ProductQuoteChangeAutoDecisionPendingEvent
 *
 * @property int $productQuoteChangeId
 */
class ProductQuoteChangeAutoDecisionPendingEvent
{
    public int $productQuoteChangeId;

    public function __construct(int $productQuoteChangeId)
    {
        $this->productQuoteChangeId = $productQuoteChangeId;
    }
}
