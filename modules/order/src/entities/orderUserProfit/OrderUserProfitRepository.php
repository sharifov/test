<?php
namespace modules\order\src\entities\orderUserProfit;

use sales\dispatchers\EventDispatcher;
use sales\repositories\Repository;
use yii\db\Exception;

/**
 * Class OrderUserProfitRepository
 * @package modules\order\src\entities\orderUserProfit
 *
 * @property EventDispatcher $eventDispatcher
 */
class OrderUserProfitRepository extends Repository
{
	/**
	 * @var EventDispatcher
	 */
	private $eventDispatcher;

	public function __construct(EventDispatcher $eventDispatcher)
	{
		$this->eventDispatcher = $eventDispatcher;
	}

	/**
	 * @param OrderUserProfit $orderUserProfit
	 * @return OrderUserProfit
	 * @throws Exception
	 */
	public function save(OrderUserProfit $orderUserProfit): OrderUserProfit
	{
		if (!$orderUserProfit->save()) {
			throw new \RuntimeException($orderUserProfit->getErrorSummary(false)[0]);
		}
		$this->eventDispatcher->dispatchAll($orderUserProfit->releaseEvents());
		return $orderUserProfit;
	}

	/**
	 * @param int $orderId
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function deleteByOrderId(int $orderId): void
	{
		foreach (OrderUserProfit::findAll(['oup_order_id' => $orderId]) as $userProfit) {
			if (!$userProfit->delete()) {
				throw new \RuntimeException('Order User Profit deleting error');
			}
		}
	}
}