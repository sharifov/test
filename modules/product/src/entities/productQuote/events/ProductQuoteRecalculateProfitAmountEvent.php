<?php

namespace modules\product\src\entities\productQuote\events;

/**
 * Class ProductQuoteRecalculateProfitAmountEvent
 * @package modules\product\src\entities\productQuote\events
 */
class ProductQuoteRecalculateProfitAmountEvent
{
    public $productQuoteId;

    /**
     * ProductQuoteRecalculateProfitAmountEvent constructor.
     * @param int $productQuoteId
     */
    public function __construct(int $productQuoteId) {
        $this->productQuoteId = $productQuoteId;
    }
}
