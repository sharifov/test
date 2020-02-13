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
    public $profitNew;
    public $profitOld;

    /**
     * ProductQuoteRecalculateProfitAmountEvent constructor.
     * @param ProductQuote $productQuote
     * @param float $profitNew
     * @param float $profitOld
     */
    public function __construct(ProductQuote $productQuote, float $profitNew, float $profitOld) {
        $this->productQuote = $productQuote;
        $this->profitNew = $profitNew;
        $this->profitOld = $profitOld;
    }
}
