<?php
namespace sales\model\user\entity\profit\listener;

use modules\product\src\entities\productQuote\events\ProductQuoteCalculateUserProfitEvent;
use sales\model\user\entity\profit\event\UserProfitRecalculateEvent;
use modules\order\src\services\OrderUserProfitService;

/**
 * Class UserProfitRecalculateEventListener
 * @package modules\product\src\listeners\productQuote
 *
 * @property OrderUserProfitService $orderUserProfitService
 */
class UserProfitRecalculateEventListener
{
	/**
	 * @var OrderUserProfitService
	 */
	private $orderUserProfitService;

	public function __construct(OrderUserProfitService $orderUserProfitService)
	{
		$this->orderUserProfitService = $orderUserProfitService;
	}

	public function handle(UserProfitRecalculateEvent $event): void
	{
		foreach ($event->order->productQuotes as $productQuote) {
			$this->orderUserProfitService->calculateUserProfit($productQuote, $event->order);
		}
	}
}