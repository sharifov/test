<?php
namespace modules\product\src\listeners\productQuote;

use modules\product\src\entities\productQuote\events\ProductQuoteCalculateUserProfitEvent;
use sales\model\user\entity\profit\service\OrderUserProfitService;

/**
 * Class ProductQuoteCalculateUserProfitEventListener
 * @package modules\product\src\listeners\productQuote
 *
 * @property OrderUserProfitService $orderUserProfitService
 */
class ProductQuoteCalculateUserProfitEventListener
{
	/**
	 * @var OrderUserProfitService
	 */
	private $orderUserProfitService;

	public function __construct(OrderUserProfitService $orderUserProfitService)
	{
		$this->orderUserProfitService = $orderUserProfitService;
	}

	public function handle(ProductQuoteCalculateUserProfitEvent $event): void
	{
		$this->orderUserProfitService->calculateUserProfit($event->productQuote->pq_id, $event->productQuote->pqOrder);
	}
}