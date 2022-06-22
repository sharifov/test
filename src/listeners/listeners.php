<?php

use src\events\quote\QuoteSendEvent;
use src\listeners\quote\QuoteSendEventListener;
use src\model\client\entity\events\ClientChangeIpEvent;
use src\model\client\entity\events\ClientCreatedEvent;
use src\model\client\entity\events\ClientExcludedEvent;
use src\model\client\listeners\ClientCreatedCheckExcludeListener;
use src\model\client\listeners\ClientExcludeNotifierListener;
use src\model\clientChat\event\ClientChatArchiveEvent;
use src\model\clientChat\event\ClientChatCloseEvent;
use src\model\clientChat\event\ClientChatHoldEvent;
use src\model\clientChat\event\ClientChatIdleEvent;
use src\model\clientChat\event\ClientChatInProgressEvent;
use src\model\clientChat\event\ClientChatNewEvent;
use src\model\clientChat\event\ClientChatPendingEvent;
use src\model\clientChat\event\ClientChatTransferEvent;
use src\model\clientChat\event\ClientChatUpdateStatusEvent;
use src\model\clientChat\event\listener\ClientChatArchiveStatusLogListener;
use src\model\clientChat\event\listener\ClientChatCloseStatusLogListener;
use src\model\clientChat\event\listener\ClientChatEndConversationListener;
use src\model\clientChat\event\listener\ClientChatHoldStatusLogListener;
use src\model\clientChat\event\listener\ClientChatIdleStatusLogListener;
use src\model\clientChat\event\listener\ClientChatInProgressStatusLogListener;
use src\model\clientChat\event\listener\ClientChatNewStatusLogListener;
use src\model\clientChat\event\listener\ClientChatPendingStatusLogListener;
use src\model\clientChat\event\listener\ClientChatRefreshListListener;
use src\model\clientChat\event\listener\ClientChatRemoveLastMessageListener;
use src\model\clientChat\event\listener\ClientChatTransferStatusLogListener;
use src\model\clientChat\event\listener\ClientChatUpdateStatusLogListener;
use src\model\clientChat\event\listener\ClientChatUserAccessSetStatusCancelListener;
use src\model\clientChatUserAccess\event\ResetChatUserAccessWidgetEvent;
use src\model\clientChatUserAccess\event\ResetChatUserAccessWidgetListener;
use src\model\clientChatUserAccess\event\UpdateChatUserAccessWidgetEvent;
use src\model\clientChatUserAccess\event\UpdateChatUserAccessWidgetListener;
use src\model\leadRedial\entity\events\CallRedialAccessCreatedEvent;
use src\model\leadRedial\entity\events\CallRedialAccessRemovedEvent;
use src\model\leadRedial\listeners\RedialCallAccessCreatedPhoneWidgetNotificationListener;
use src\model\leadRedial\listeners\RedialCallAccessCreatedUserNotificationListener;
use src\model\leadRedial\listeners\RemoveRedialCallUserNotificationListener;
use src\model\user\entity\profit\event\UserProfitCalculateByOrderTipsUserProfitsEvent;
use src\model\user\entity\profit\listener\UserProfitCalculateByOrderTipsUserProfitsEventListener;
use src\model\visitorSubscription\event\VisitorSubscriptionEnabled;
use src\model\visitorSubscription\listener\FindChatsAndRunDistributionLogic;
use src\events\quote\QuoteExtraMarkUpChangeEvent;
use src\listeners\quote\QuoteExtraMarkUpChangeEventListener;
use src\entities\email\events\EmailDeletedEvent;
use src\listeners\email\EmailDeletedEventListener;

return [
    EmailDeletedEvent::class => [EmailDeletedEventListener::class],

    UserProfitCalculateByOrderTipsUserProfitsEvent::class => [UserProfitCalculateByOrderTipsUserProfitsEventListener::class],
    QuoteSendEvent::class => [QuoteSendEventListener::class],
    QuoteExtraMarkUpChangeEvent::class => [
        QuoteExtraMarkUpChangeEventListener::class,
    ],
    UpdateChatUserAccessWidgetEvent::class => [UpdateChatUserAccessWidgetListener::class],
    ResetChatUserAccessWidgetEvent::class => [ResetChatUserAccessWidgetListener::class],
    ClientChatPendingEvent::class => [
        ClientChatPendingStatusLogListener::class
    ],
    ClientChatNewEvent::class => [
        ClientChatNewStatusLogListener::class
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
    ],
    ClientExcludedEvent::class => [
        ClientExcludeNotifierListener::class,
    ],
    ClientCreatedEvent::class => [
        ClientCreatedCheckExcludeListener::class,
    ],
    ClientChangeIpEvent::class => [
        ClientCreatedCheckExcludeListener::class,
    ],
    ClientChatUpdateStatusEvent::class => [
        ClientChatUpdateStatusLogListener::class
    ],

    VisitorSubscriptionEnabled::class => [
        FindChatsAndRunDistributionLogic::class
    ],

//    ClientChatOwnerAssignedEvent::class => [ClientChatRemoveOldOwnerUnreadMessagesListener::class],

    CallRedialAccessCreatedEvent::class => [
        RedialCallAccessCreatedPhoneWidgetNotificationListener::class,
        RedialCallAccessCreatedUserNotificationListener::class,
    ],

    CallRedialAccessRemovedEvent::class => [
        RemoveRedialCallUserNotificationListener::class,
    ],

];
