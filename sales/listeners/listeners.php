<?php

use sales\events\quote\QuoteSendEvent;
use sales\listeners\quote\QuoteSendEventListener;
use sales\model\clientChat\event\ClientChatCloseEvent;
use sales\model\clientChat\event\ClientChatEndConversationListener;
use sales\model\clientChat\event\ClientChatManageStatusLogEvent;
use sales\model\clientChat\event\ClientChatManageStatusLogListener;
use sales\model\clientChat\event\ClientChatOwnerAssignedEvent;
use sales\model\clientChat\event\ClientChatRemoveLastMessageListener;
use sales\model\clientChat\event\ClientChatRemoveOldOwnerUnreadMessagesListener;
use sales\model\clientChat\event\ClientChatSetStatusIdleEvent;
use sales\model\clientChat\event\ClientChatSetStatusIdleListener;
use sales\model\clientChat\event\ClientChatUserAccessSetStatusCancelListener;
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
    ClientChatManageStatusLogEvent::class => [ClientChatManageStatusLogListener::class],
    ClientChatSetStatusIdleEvent::class => [ClientChatSetStatusIdleListener::class],
    ClientChatCloseEvent::class => [
        ClientChatEndConversationListener::class,
        ClientChatRemoveLastMessageListener::class,
        ClientChatUserAccessSetStatusCancelListener::class
    ],

//    ClientChatOwnerAssignedEvent::class => [ClientChatRemoveOldOwnerUnreadMessagesListener::class],
];
