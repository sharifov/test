<?php
namespace modules\order\src\entities\orderTipsUserProfit;

use sales\repositories\Repository;

class OrderTipsUserProfitRepository extends Repository
{
	public function findByOrderId(int $id)
	{
		$orderTipsUserProfit = OrderTipsUserProfit::find()->where(['otup_order_id' => $id])->all();
		if (!$orderTipsUserProfit) {
			throw new \RuntimeException('Not Found order tips user profit by order id');
		}
		return $orderTipsUserProfit;
	}

	public function save(OrderTipsUserProfit $orderTipsUserProfit): OrderTipsUserProfit
	{
		if (!$orderTipsUserProfit->save()) {
			throw new \RuntimeException($orderTipsUserProfit->getErrorSummary(false)[0]);
		}
		return $orderTipsUserProfit;
	}

	public function deleteByOrderId(int $orderId): void
	{
		foreach (OrderTipsUserProfit::findAll(['otup_order_id' => $orderId]) as $tipsUserProfit) {
			if (!$tipsUserProfit->delete()) {
				throw new \RuntimeException('Order Tips User Profit deleting error');
			}
		}
	}
}