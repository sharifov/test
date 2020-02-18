<?php
namespace modules\order\src\entities\orderUserProfit;

use sales\repositories\Repository;
use yii\db\Exception;

class OrderUserProfitRepository extends Repository
{
	/**
	 * @param OrderUserProfit $orderUserProfit
	 * @return OrderUserProfit
	 * @throws Exception
	 */
	public function save(OrderUserProfit $orderUserProfit): OrderUserProfit
	{
		if (!$orderUserProfit->save()) {
			throw new Exception($orderUserProfit->getErrorSummary(false)[0]);
		}
		return $orderUserProfit;
	}
}