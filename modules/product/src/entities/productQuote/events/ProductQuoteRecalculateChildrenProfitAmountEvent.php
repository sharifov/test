<?php

namespace modules\product\src\entities\productQuote\events;

use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class ProductQuoteRecalculateChildrenProfitAmountEvent
 * @package modules\product\src\entities\productQuote\events
 */
class ProductQuoteRecalculateChildrenProfitAmountEvent
{
    public $productQuote;

    /**
     * ProductQuoteRecalculateChildrenProfitAmountEvent constructor.
     * @param ProductQuote $productQuote
     */
    public function __construct(ProductQuote $productQuote)
    {
        $this->productQuote = $productQuote;
    }
}
