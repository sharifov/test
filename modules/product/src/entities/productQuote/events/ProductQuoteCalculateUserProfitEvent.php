<?php

namespace modules\product\src\entities\productQuote\events;


use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class ProductQuoteCalculateUserProfitEvent
 * @package modules\product\src\entities\productQuote\events
 *
 * @property ProductQuote $productQuote
 */
class ProductQuoteCalculateUserProfitEvent
{
	public $productQuote;

	public function __construct(ProductQuote $productQuote)
	{
		$this->productQuote = $productQuote;
	}
}