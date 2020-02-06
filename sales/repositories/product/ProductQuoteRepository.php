<?php

namespace sales\repositories\product;

use modules\product\src\entities\productQuote\ProductQuote;
use sales\repositories\Repository;

class ProductQuoteRepository extends Repository
{
	/**
	 * @param ProductQuote $productQuote
	 * @return int
	 */
	public function save(ProductQuote $productQuote): int
	{
		if (!$productQuote->save()) {
			throw new \RuntimeException($productQuote->getErrorSummary(false)[0]);
		}
		return $productQuote->pq_id;
	}
}