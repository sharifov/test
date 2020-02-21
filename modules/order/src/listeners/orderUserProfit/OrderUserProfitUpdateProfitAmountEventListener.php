<?php
namespace modules\order\src\listeners\orderUserProfit;

use modules\order\src\entities\order\events\OrderUserProfitUpdateProfitAmountEvent;
use modules\order\src\entities\orderUserProfit\OrderUserProfitRepository;

/**
 * Class OrderUserProfitUpdateProfitAmountEventListener
 * @package modules\order\src\listeners
 *
 * @property OrderUserProfitRepository $orderUserProfitRepository
 */
class OrderUserProfitUpdateProfitAmountEventListener
{
	/**
	 * @var OrderUserProfitRepository
	 */
	private $orderUserProfitRepository;

	public function __construct(OrderUserProfitRepository $orderUserProfitRepository)
	{
		$this->orderUserProfitRepository = $orderUserProfitRepository;
	}

	/**
	 * @param OrderUserProfitUpdateProfitAmountEvent $event
	 * @throws \yii\db\Exception
	 */
	public function handle(OrderUserProfitUpdateProfitAmountEvent $event)
	{
		if ($event->order && $event->order->orderUserProfit) {
			foreach ($event->order->orderUserProfit as $orderUserProfit) {
				$orderUserProfit->updateAmount($event->order->or_profit_amount ?? 0.00);
				$this->orderUserProfitRepository->save($orderUserProfit);
			}
		}
	}
}