<?php

namespace sales\repositories\product;

use common\models\Product;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

class ProductRepository extends Repository
{
	/**
	 * @param int $id
	 * @return Product
	 */
	public function find(int $id): Product
	{
		if ($product = Product::findOne($id)) {
			return $product;
		}
		throw new NotFoundException('Product is not found', 100);
	}
}