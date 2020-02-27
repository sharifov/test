<?php

namespace modules\product\src\entities\productQuote\events;

use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class ProductQuoteRecalculateProfitAmountEvent
 * @package modules\product\src\entities\productQuote\events
 *
 * @property ProductQuote $productQuote
 * @property bool $resetDispatcherQueue
 */
class ProductQuoteRecalculateProfitAmountEvent
{
	/**
	 * @var ProductQuote
	 */
    public $productQuote;

	/**
	 * @var bool
	 */
	public $resetDispatcherQueue;

	/**
	 * ProductQuoteRecalculateProfitAmountEvent constructor.
	 * @param ProductQuote $productQuote
	 * @param bool $resetDispatcherQueue
	 */
    public function __construct(ProductQuote $productQuote, bool $resetDispatcherQueue = false) {
        $this->productQuote = $productQuote;
		$this->resetDispatcherQueue = $resetDispatcherQueue;
	}
}
