<?php

use sales\model\user\entity\profit\event\UserProfitCalculateByOrderTipsUserProfitsEvent;
use sales\model\user\entity\profit\listener\UserProfitCalculateByOrderTipsUserProfitsEventListener;

return [
	UserProfitCalculateByOrderTipsUserProfitsEvent::class => [UserProfitCalculateByOrderTipsUserProfitsEventListener::class]
];
