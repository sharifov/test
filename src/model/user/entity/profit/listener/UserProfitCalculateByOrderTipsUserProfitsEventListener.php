<?php

namespace src\model\user\entity\profit\listener;

use src\model\user\entity\profit\event\UserProfitCalculateByOrderTipsUserProfitsEvent;
use src\services\user\profit\UserProfitCalculateService;

/**
 * Class UserProfitCalculateByOrderTipsUserProfitsEventListener
 * @package src\model\user\entity\profit\listener
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
