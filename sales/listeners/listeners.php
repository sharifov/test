<?php

use sales\events\quote\QuoteSendEvent;
use sales\listeners\quote\QuoteSendEventListener;
use sales\model\clientChatUserAccess\event\SendNotificationEvent;
use sales\model\clientChatUserAccess\event\SendNotificationListener;
use sales\model\user\entity\profit\event\UserProfitCalculateByOrderTipsUserProfitsEvent;
use sales\model\user\entity\profit\listener\UserProfitCalculateByOrderTipsUserProfitsEventListener;

return [
	UserProfitCalculateByOrderTipsUserProfitsEvent::class => [UserProfitCalculateByOrderTipsUserProfitsEventListener::class],
	QuoteSendEvent::class => [QuoteSendEventListener::class],
	SendNotificationEvent::class => [SendNotificationListener::class]
];
