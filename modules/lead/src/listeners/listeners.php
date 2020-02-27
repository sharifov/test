<?php

use sales\events\lead\LeadBookedEvent;
use sales\events\lead\LeadCallExpertRequestEvent;
use sales\events\lead\LeadCountPassengersChangedEvent;
use sales\events\lead\LeadCreatedByApiBOEvent;
use sales\events\lead\LeadCreatedByApiEvent;
use sales\events\lead\LeadCreatedByIncomingCallEvent;
use sales\events\lead\LeadCreatedByIncomingEmailEvent;
use sales\events\lead\LeadCreatedByIncomingSmsEvent;
use sales\events\lead\LeadCreatedCloneByUserEvent;
use sales\events\lead\LeadCreatedEvent;
use sales\events\lead\LeadCreatedManuallyEvent;
use sales\events\lead\LeadDuplicateDetectedEvent;
use sales\events\lead\LeadFollowUpEvent;
use sales\events\lead\LeadOwnerChangedEvent;
use sales\events\lead\LeadPendingEvent;
use sales\events\lead\LeadPreferencesUpdateCurrencyEvent;
use sales\events\lead\LeadProcessingEvent;
use sales\events\lead\LeadQuoteCloneEvent;
use sales\events\lead\LeadRejectEvent;
use sales\events\lead\LeadSnoozeEvent;
use sales\events\lead\LeadSoldEvent;
use sales\events\lead\LeadTaskEvent;
use sales\events\lead\LeadTrashEvent;
use sales\listeners\lead\LeadBookedEventLogListener;
use sales\listeners\lead\LeadBookedNotificationsListener;
use sales\listeners\lead\LeadCallExpertRequestEventListener;
use sales\listeners\lead\LeadCountPassengersChangedEventListener;
use sales\listeners\lead\LeadCreatedByApiBOLogEventListener;
use sales\listeners\lead\LeadCreatedByApiLogEventListener;
use sales\listeners\lead\LeadCreatedByIncomingCallLogListener;
use sales\listeners\lead\LeadCreatedByIncomingEmailLogListener;
use sales\listeners\lead\LeadCreatedByIncomingSmsLogListener;
use sales\listeners\lead\LeadCreatedCloneByUserEventListener;
use sales\listeners\lead\LeadCreatedEventListener;
use sales\listeners\lead\LeadDuplicateDetectedEventListener;
use sales\listeners\lead\LeadFollowUpEventLogListener;
use sales\listeners\lead\LeadFollowUpNotificationsListener;
use sales\listeners\lead\LeadOwnerChangedNotificationsListener;
use sales\listeners\lead\LeadPendingEventLogListener;
use sales\listeners\lead\LeadPreferencesUpdateCurrencyEventListener;
use sales\listeners\lead\LeadProcessingEventLogListener;
use sales\listeners\lead\LeadQcallAddListener;
use sales\listeners\lead\LeadQuoteCloneEventListener;
use sales\listeners\lead\LeadRejectEventLogListener;
use sales\listeners\lead\LeadSnoozeEventLogListener;
use sales\listeners\lead\LeadSnoozeNotificationsListener;
use sales\listeners\lead\LeadSoldEventLogListener;
use sales\listeners\lead\LeadSoldNotificationsListener;
use sales\listeners\lead\LeadTaskEventListener;
use sales\listeners\lead\LeadTrashEventLogListener;

return [
    LeadCreatedEvent::class => [LeadCreatedEventListener::class],
    LeadCreatedManuallyEvent::class => [],
    LeadCreatedByIncomingCallEvent::class => [
        LeadCreatedByIncomingCallLogListener::class,
        LeadQcallAddListener::class,
    ],
    LeadCreatedByApiEvent::class => [
        LeadCreatedByApiLogEventListener::class,
        LeadQcallAddListener::class,
    ],
    LeadCreatedByApiBOEvent::class => [
        LeadCreatedByApiBOLogEventListener::class,
        LeadQcallAddListener::class,
    ],
    LeadCreatedByIncomingSmsEvent::class => [LeadCreatedByIncomingSmsLogListener::class],
    LeadCreatedByIncomingEmailEvent::class => [LeadCreatedByIncomingEmailLogListener::class],

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
	LeadPreferencesUpdateCurrencyEvent::class => [LeadPreferencesUpdateCurrencyEventListener::class]
];
