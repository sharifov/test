<?php

namespace modules\product\src\entities\product\events;

use modules\product\src\entities\product\Product;

/**
 * Class ProductMarketPriceChangedEvent
 *
 * @property Product $product
 */
class ProductMarketPriceChangedEvent
{
    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
