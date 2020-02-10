<?php

namespace modules\product\src\entities\productQuoteOption\events;

use modules\product\src\entities\productQuoteOption\ProductQuoteOption;

/**
 * Class ProductQuoteOptionCloneCreatedEvent
 *
 * @property ProductQuoteOption $option
 */
class ProductQuoteOptionCloneCreatedEvent
{
    public $option;

    public function __construct(ProductQuoteOption $option)
    {
        $this->option = $option;
    }
}
