<?php

namespace common\bootstrap;

use sales\dispatchers\DeferredEventDispatcher;
use sales\dispatchers\EventDispatcher;
use sales\dispatchers\SimpleEventDispatcher;
use sales\entities\cases\events\CasesFollowUpStatusEvent;
use sales\entities\cases\events\CasesPendingStatusEvent;
use sales\entities\cases\events\CasesProcessingStatusEvent;
use sales\entities\cases\events\CasesSolvedStatusEvent;
use sales\entities\cases\events\CasesTrashStatusEvent;
use sales\events\lead\LeadBookedEvent;
use sales\events\lead\LeadCallExpertRequestEvent;
use sales\events\lead\LeadCreatedCloneByUserEvent;
use sales\events\lead\LeadCreatedEvent;
use sales\events\lead\LeadDuplicateDetectedEvent;
use sales\events\lead\LeadFollowUpEvent;
use sales\events\lead\LeadOwnerChangedEvent;
use sales\events\lead\LeadCountPassengersChangedEvent;
use sales\events\lead\LeadPendingEvent;
use sales\events\lead\LeadProcessingEvent;
use sales\events\lead\LeadQuoteCloneEvent;
use sales\events\lead\LeadRejectEvent;
use sales\events\lead\LeadSnoozeEvent;
use sales\events\lead\LeadSoldEvent;
use sales\events\lead\LeadTaskEvent;
use sales\events\lead\LeadTrashEvent;
use sales\listeners\cases\CasesFollowUpStatusEventLogListener;
use sales\listeners\cases\CasesPendingStatusEventLogListener;
use sales\listeners\cases\CasesProcessingStatusEventLogListener;
use sales\listeners\cases\CasesSolvedStatusEventLogListener;
use sales\listeners\cases\CasesTrashStatusEventLogListener;
use sales\listeners\lead\LeadBookedEventLogListener;
use sales\listeners\lead\LeadBookedNotificationsListener;
use sales\listeners\lead\LeadCallExpertRequestEventListener;
use sales\listeners\lead\LeadCreatedCloneByUserEventListener;
use sales\listeners\lead\LeadCreatedEventListener;
use sales\listeners\lead\LeadDuplicateDetectedEventListener;
use sales\listeners\lead\LeadFollowUpEventLogListener;
use sales\listeners\lead\LeadFollowUpNotificationsListener;
use sales\listeners\lead\LeadOwnerChangedNotificationsListener;
use sales\listeners\lead\LeadCountPassengersChangedEventListener;
use sales\listeners\lead\LeadPendingEventLogListener;
use sales\listeners\lead\LeadProcessingEventLogListener;
use sales\listeners\lead\LeadQuoteCloneEventListener;
use sales\listeners\lead\LeadRejectEventLogListener;
use sales\listeners\lead\LeadSnoozeEventLogListener;
use sales\listeners\lead\LeadSnoozeNotificationsListener;
use sales\listeners\lead\LeadSoldEventLogListener;
use sales\listeners\lead\LeadSoldNotificationsListener;
use sales\listeners\lead\LeadTaskEventListener;
use sales\listeners\lead\LeadTrashEventLogListener;
use yii\base\BootstrapInterface;
use yii\di\Container;

class SetUp implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = \Yii::$container;

        $container->setSingleton(EventDispatcher::class, DeferredEventDispatcher::class);

        $container->setSingleton(DeferredEventDispatcher::class, function (Container $container) {
            return new DeferredEventDispatcher(new SimpleEventDispatcher($container, [
                LeadCreatedEvent::class => [LeadCreatedEventListener::class],
                LeadDuplicateDetectedEvent::class => [LeadDuplicateDetectedEventListener::class],
                LeadOwnerChangedEvent::class => [LeadOwnerChangedNotificationsListener::class],
                LeadCallExpertRequestEvent::class => [LeadCallExpertRequestEventListener::class],
                LeadTaskEvent::class => [LeadTaskEventListener::class],
                LeadCountPassengersChangedEvent::class => [LeadCountPassengersChangedEventListener::class],
                LeadCreatedCloneByUserEvent::class => [LeadCreatedCloneByUserEventListener::class],

                LeadPendingEvent::class => [LeadPendingEventLogListener::class],
                LeadProcessingEvent::class => [
                    LeadProcessingEventLogListener::class,
                ],
                LeadRejectEvent::class => [LeadRejectEventLogListener::class],
                LeadFollowUpEvent::class => [
                    LeadFollowUpEventLogListener::class,
                    LeadFollowUpNotificationsListener::class,
                ],
                LeadSoldEvent::class => [
                    LeadSoldEventLogListener::class,
                    LeadSoldNotificationsListener::class,
                ],
                LeadTrashEvent::class => [LeadTrashEventLogListener::class],
                LeadBookedEvent::class => [
                    LeadBookedEventLogListener::class,
                    LeadBookedNotificationsListener::class,
                ],
                LeadSnoozeEvent::class => [
                    LeadSnoozeEventLogListener::class,
                    LeadSnoozeNotificationsListener::class,
                ],

                LeadQuoteCloneEvent::class => [LeadQuoteCloneEventListener::class],

                CasesPendingStatusEvent::class => [CasesPendingStatusEventLogListener::class],
                CasesProcessingStatusEvent::class => [CasesProcessingStatusEventLogListener::class],
                CasesFollowUpStatusEvent::class => [CasesFollowUpStatusEventLogListener::class],
                CasesSolvedStatusEvent::class => [CasesSolvedStatusEventLogListener::class],
                CasesTrashStatusEvent::class => [CasesTrashStatusEventLogListener::class],
            ]));
        });
    }

}
