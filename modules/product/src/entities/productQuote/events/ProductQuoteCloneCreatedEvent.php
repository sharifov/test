<?php

namespace modules\product\src\entities\productQuote\events;

use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class ProductQuoteCloneCreatedEvent
 *
 * @property ProductQuote $quote
 */
class ProductQuoteCloneCreatedEvent
{
    public $quote;

    public function __construct(ProductQuote $quote)
    {
        $this->quote = $quote;
    }
}
