<?php
namespace modules\order\src\entities\orderTips;

use sales\repositories\Repository;

class OrderTipsRepository extends Repository
{

	public function save(OrderTips $orderTips): OrderTips
	{
		if (!$orderTips->save()) {
			throw new \RuntimeException('Order tips saving error');
		}
		return $orderTips;
	}
}