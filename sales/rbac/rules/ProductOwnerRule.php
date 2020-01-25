<?php

namespace sales\rbac\rules;

use common\models\Product;

class ProductOwnerRule extends ProductRule
{
	public $name = 'isProductOwner';

	public function getData(int $userId, Product $product)
	{
		return true;
	}
}