<?php

use sales\events\quote\QuoteSendEvent;
use sales\listeners\quote\QuoteSendEventListener;
use sales\model\clientChatUserAccess\event\ResetChatUserAccessWidgetEvent;
use sales\model\clientChatUserAccess\event\ResetChatUserAccessWidgetListener;
use sales\model\clientChatUserAccess\event\UpdateChatUserAccessWidgetEvent;
use sales\model\clientChatUserAccess\event\UpdateChatUserAccessWidgetListener;
use sales\model\user\entity\profit\event\UserProfitCalculateByOrderTipsUserProfitsEvent;
use sales\model\user\entity\profit\listener\UserProfitCalculateByOrderTipsUserProfitsEventListener;

return [
	UserProfitCalculateByOrderTipsUserProfitsEvent::class => [UserProfitCalculateByOrderTipsUserProfitsEventListener::class],
	QuoteSendEvent::class => [QuoteSendEventListener::class],
	UpdateChatUserAccessWidgetEvent::class => [UpdateChatUserAccessWidgetListener::class],
	ResetChatUserAccessWidgetEvent::class => [ResetChatUserAccessWidgetListener::class],
];
