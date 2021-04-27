<?php

namespace modules\product\src\entities\product\events;

use modules\product\src\entities\product\Product;

/**
 * Class ProductClonedEvent
 *
 * @property Product $product
 */
class ProductClonedEvent
{
    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
