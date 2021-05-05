<?php

namespace modules\product\src\entities\productQuote\events;

use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class ProductQuoteReplacedEvent
 *
 * @property ProductQuote $productQuote
 * @property int $originProductQuoteId
 */
class ProductQuoteReplaceEvent
{
    public $productQuote;
    public $originProductQuoteId;

    /**
     * @param ProductQuote $productQuote
     * @param int $originProductQuoteId
     */
    public function __construct(ProductQuote $productQuote, int $originProductQuoteId)
    {
        $this->productQuote = $productQuote;
        $this->originProductQuoteId = $originProductQuoteId;
    }
}
