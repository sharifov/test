<?php

namespace modules\product\src\entities\productQuote\events;

/**
 * Class ProductQuoteRecalculateProfitAmountEvent
 * @package modules\product\src\entities\productQuote\events
 */
class ProductQuoteRecalculateProfitAmountEvent
{
    public $productQuoteId;
    public $profitNew;

    /**
     * ProductQuoteRecalculateProfitAmountEvent constructor.
     * @param int $productQuoteId
     * @param float $profitOld
     * @param float $profitNew
     */
    public function __construct(int $productQuoteId, float $profitNew) {
        $this->productQuoteId = $productQuoteId;
        $this->profitNew = $profitNew;
    }
}
