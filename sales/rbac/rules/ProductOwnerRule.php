<?php

namespace sales\rbac\rules;

use modules\product\src\entities\product\Product;

class ProductOwnerRule extends ProductRule
{
	public $name = 'isProductOwner';

	public function getData(int $userId, Product $product)
	{
		return true;
	}
}