<?php

namespace modules\product\src\entities\productQuote\events;

/**
 * Class ProductQuoteSoldEvent
 * @package modules\product\src\entities\productQuote\events
 */
class ProductQuoteSoldEvent implements ProductQuotable
{
    public $productQuoteId;

    /**
     * ProductQuoteSoldEvent constructor.
     * @param int $productQuoteId
     */
    public function __construct(int $productQuoteId)
    {
        $this->productQuoteId = $productQuoteId;
    }

    public function getProductQuoteId()
    {
        return $this->productQuoteId;
    }
}
