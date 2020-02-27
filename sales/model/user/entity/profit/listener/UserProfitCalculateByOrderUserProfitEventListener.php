<?php
namespace sales\model\user\entity\profit\listener;

use sales\model\user\entity\profit\event\UserProfitCalculateByOrderUserProfitEvent;
use sales\services\user\profit\UserProfitCalculateService;

/**
 * Class UserProfitCalculateByOrderUserProfitEventListener
 * @package modules\product\src\listeners\productQuote
 *
 * @property UserProfitCalculateService $userProfitCalculateService
 */
class UserProfitCalculateByOrderUserProfitEventListener
{
	/**
	 * @var UserProfitCalculateService
	 */
	private $userProfitCalculateService;

	public function __construct(UserProfitCalculateService $userProfitCalculateService)
	{
		$this->userProfitCalculateService = $userProfitCalculateService;
	}

	public function handle(UserProfitCalculateByOrderUserProfitEvent $event): void
	{
		foreach ($event->order->productQuotes as $productQuote) {
			$this->userProfitCalculateService->calculateByOrderUserProfit($productQuote, $event->order);
		}
	}
}