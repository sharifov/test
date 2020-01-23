<?php

namespace modules\product\src\entities\product\events;

use common\models\Product;

/**
 * Class ProductCreateEvent
 *
 * @property Product $product
 */
class ProductCreateEvent
{
    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
