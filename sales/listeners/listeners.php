<?php

use sales\events\quote\QuoteSendEvent;
use sales\listeners\quote\QuoteSendEventListener;
use sales\model\clientChat\event\ClientChatArchiveEvent;
use sales\model\clientChat\event\ClientChatCloseEvent;
use sales\model\clientChat\event\ClientChatHoldEvent;
use sales\model\clientChat\event\ClientChatIdleEvent;
use sales\model\clientChat\event\ClientChatInProgressEvent;
use sales\model\clientChat\event\ClientChatPendingEvent;
use sales\model\clientChat\event\ClientChatTransferEvent;
use sales\model\clientChat\event\listener\ClientChatArchiveStatusLogListener;
use sales\model\clientChat\event\listener\ClientChatCloseStatusLogListener;
use sales\model\clientChat\event\listener\ClientChatEndConversationListener;
use sales\model\clientChat\event\listener\ClientChatHoldStatusLogListener;
use sales\model\clientChat\event\listener\ClientChatIdleStatusLogListener;
use sales\model\clientChat\event\listener\ClientChatInProgressStatusLogListener;
use sales\model\clientChat\event\listener\ClientChatPendingStatusLogListener;
use sales\model\clientChat\event\listener\ClientChatRefreshListListener;
use sales\model\clientChat\event\listener\ClientChatRemoveLastMessageListener;
use sales\model\clientChat\event\listener\ClientChatTransferStatusLogListener;
use sales\model\clientChat\event\listener\ClientChatUserAccessSetStatusCancelListener;
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
    ClientChatPendingEvent::class => [
        ClientChatPendingStatusLogListener::class
    ],
    ClientChatCloseEvent::class => [
        ClientChatCloseStatusLogListener::class,
        ClientChatEndConversationListener::class,
        ClientChatRemoveLastMessageListener::class,
        ClientChatUserAccessSetStatusCancelListener::class
    ],
    ClientChatArchiveEvent::class => [
        ClientChatArchiveStatusLogListener::class,
        ClientChatEndConversationListener::class,
        ClientChatRemoveLastMessageListener::class,
        ClientChatUserAccessSetStatusCancelListener::class,
        ClientChatRefreshListListener::class,
    ],
    ClientChatTransferEvent::class => [
        ClientChatTransferStatusLogListener::class
    ],
    ClientChatInProgressEvent::class => [
        ClientChatInProgressStatusLogListener::class
    ],
    ClientChatHoldEvent::class => [
        ClientChatHoldStatusLogListener::class
    ],
    ClientChatIdleEvent::class => [
        ClientChatIdleStatusLogListener::class
    ]

//    ClientChatOwnerAssignedEvent::class => [ClientChatRemoveOldOwnerUnreadMessagesListener::class],
];
