<?php

namespace modules\product\src\entities\productQuote\events;

use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class ProductQuoteRecalculateProfitAmountEvent
 * @package modules\product\src\entities\productQuote\events
 */
class ProductQuoteRecalculateProfitAmountEvent
{
    public $productQuote;

    /**
     * ProductQuoteRecalculateProfitAmountEvent constructor.
     * @param ProductQuote $productQuote
     */
    public function __construct(ProductQuote $productQuote) {
        $this->productQuote = $productQuote;
    }
}
