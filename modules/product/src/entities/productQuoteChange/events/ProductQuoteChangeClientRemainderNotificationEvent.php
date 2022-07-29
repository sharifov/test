<?php

namespace modules\product\src\entities\productQuoteChange\events;

/**
 * Class ProductQuoteChangeClientRemainderNotificationEvent
 *
 * @property int $productQuoteChangeId
 */
class ProductQuoteChangeClientRemainderNotificationEvent
{
    public int $productQuoteChangeId;

    public function __construct(int $productQuoteChangeId)
    {
        $this->productQuoteChangeId = $productQuoteChangeId;
    }
}
