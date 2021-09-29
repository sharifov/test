<?php

namespace modules\product\src\abac\dto;

use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class ProductQuoteAbacDto
 * @package modules\product\src\abac\dto
 *
 * @property bool $is_new
 */
class ProductQuoteAbacDto extends \stdClass
{
    public bool $is_new;

    public function __construct(?ProductQuote $productQuote)
    {
        if ($productQuote) {
            $this->is_new = $productQuote->isNew();
        }
    }
}
