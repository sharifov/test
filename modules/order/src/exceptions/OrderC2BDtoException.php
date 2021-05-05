<?php

namespace modules\order\src\exceptions;

use modules\product\src\interfaces\Productable;

/**
 * Class OrderC2BDtoException
 * @package modules\order\src\exceptions
 *
 * @property Productable $product
 * @property string $quoteOtaId
 */
class OrderC2BDtoException
{
    public $product;

    public $quoteOtaId;

    public function __construct(Productable $product, string $quoteOtaId)
    {
        $this->product = $product;
        $this->quoteOtaId = $quoteOtaId;
    }
}
