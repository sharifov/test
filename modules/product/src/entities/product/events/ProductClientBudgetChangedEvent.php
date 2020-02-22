<?php

namespace modules\product\src\entities\product\events;

use modules\product\src\entities\product\Product;

/**
 * Class ProductClientBudgetChangedEvent
 *
 * @property Product $product
 */
class ProductClientBudgetChangedEvent
{
    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
