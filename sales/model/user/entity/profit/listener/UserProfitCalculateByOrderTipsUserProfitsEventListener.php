<?php

namespace sales\model\user\entity\profit\listener;

use sales\model\user\entity\profit\event\UserProfitCalculateByOrderTipsUserProfitsEvent;
use sales\services\user\profit\UserProfitCalculateService;

/**
 * Class UserProfitCalculateByOrderTipsUserProfitsEventListener
 * @package sales\model\user\entity\profit\listener
 *
 * @property UserProfitCalculateService $userProfitCalculateService
 */
class UserProfitCalculateByOrderTipsUserProfitsEventListener
{
	/**
	 * @var UserProfitCalculateService
	 */
	private $userProfitCalculateService;

	public function __construct(UserProfitCalculateService $userProfitCalculateService)
	{

		$this->userProfitCalculateService = $userProfitCalculateService;
	}

	public function handle(UserProfitCalculateByOrderTipsUserProfitsEvent $event): void
	{
		$this->userProfitCalculateService->calculateByTipsUserProfit($event->order);
	}
}