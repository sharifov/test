<?php

use modules\objectTask\src\listeners\NoAnswerProtocolCancelListener;
use modules\objectTask\src\listeners\NoAnswerProtocolListener;
use modules\shiftSchedule\src\events\ShiftScheduleEventChangedEvent;
use modules\shiftSchedule\src\listeners\ShiftScheduleEventChangedListener;
use modules\smartLeadDistribution\src\listeners\LeadRatingCalculationListener;
use src\events\lead\LeadBookedEvent;
use src\events\lead\LeadCallExpertChangedEvent;
use src\events\lead\LeadCallExpertRequestEvent;
use src\events\lead\LeadCloseEvent;
use src\events\lead\LeadCountPassengersChangedEvent;
use src\events\lead\LeadCreatedByApiBOEvent;
use src\events\lead\LeadCreatedByApiEvent;
use src\events\lead\LeadCreatedByIncomingCallEvent;
use src\events\lead\LeadCreatedByIncomingEmailEvent;
use src\events\lead\LeadCreatedByIncomingSmsEvent;
use src\events\lead\LeadCreatedClientChatEvent;
use src\events\lead\LeadCreatedCloneByUserEvent;
use src\events\lead\LeadCreatedEvent;
use src\events\lead\LeadCreatedManuallyEvent;
use src\events\lead\LeadCreatedNewEvent;
use src\events\lead\LeadDuplicateDetectedEvent;
use src\events\lead\LeadExtraQueueEvent;
use src\events\lead\LeadFollowUpEvent;
use src\events\lead\LeadNewEvent;
use src\events\lead\LeadOwnerChangedEvent;
use src\events\lead\LeadPendingEvent;
use src\events\lead\LeadPoorProcessingEvent;
use src\events\lead\LeadPreferencesUpdateCurrencyEvent;
use src\events\lead\LeadProcessingEvent;
use src\events\lead\LeadQuoteCloneEvent;
use src\events\lead\LeadReceiveEmailEvent;
use src\events\lead\LeadRejectEvent;
use src\events\lead\LeadSnoozeEvent;
use src\events\lead\LeadSoldEvent;
use src\events\lead\LeadStatusChangedEvent;
use src\events\lead\LeadTaskEvent;
use src\events\lead\LeadTrashEvent;
use src\listeners\lead\LeadBookedEventLogListener;
use src\listeners\lead\LeadBookedNotificationsListener;
use src\events\lead\LeadBusinessExtraQueueEvent;
use src\listeners\lead\leadBusinessExtraQueue\LeadBusinessExtraQueueNotificationsListener;
use src\listeners\lead\LeadCallExpertChangedListener;
use src\listeners\lead\LeadCallExpertRequestEventListener;
use src\listeners\lead\LeadCloseListener;
use src\listeners\lead\LeadCountPassengersChangedEventListener;
use src\listeners\lead\LeadCreatedByApiBOLogEventListener;
use src\listeners\lead\LeadCreatedByApiLogEventListener;
use src\listeners\lead\LeadCreatedByIncomingCallLogListener;
use src\listeners\lead\LeadCreatedByIncomingEmailLogListener;
use src\listeners\lead\LeadCreatedByIncomingSmsLogListener;
use src\listeners\lead\LeadCreatedClientChatLogListener;
use src\listeners\lead\LeadCreatedCloneByUserEventListener;
use src\listeners\lead\LeadCreatedEventListener;
use src\listeners\lead\LeadCreatedNewEventLogListener;
use src\listeners\lead\LeadDuplicateDetectedEventListener;
use src\listeners\lead\LeadExtraQueueEventLogListener;
use src\listeners\lead\LeadExtraQueueNotificationsListener;
use src\listeners\lead\LeadFollowUpEventLogListener;
use src\listeners\lead\LeadFollowUpNotificationsListener;
use src\listeners\lead\LeadFromSnoozeNotificationListener;
use src\listeners\lead\LeadNewEventLogListener;
use src\listeners\lead\LeadOwnerChangedNotificationsListener;
use src\listeners\lead\LeadPendingEventLogListener;
use src\listeners\lead\LeadPhoneTrustListener;
use src\listeners\lead\LeadPoorProcessingAdderListener;
use src\listeners\lead\LeadPoorProcessingRemoverListener;
use src\listeners\lead\LeadPoorProcessingRemoverOwnerChangedListener;
use src\listeners\lead\LeadPreferencesUpdateCurrencyEventListener;
use src\listeners\lead\LeadProcessingEventLogListener;
use src\listeners\lead\LeadQcallAddListener;
use src\listeners\lead\LeadQcallProcessingListener;
use src\listeners\lead\LeadQuoteCloneEventListener;
use src\listeners\lead\LeadRejectClientReturnIndicationListener;
use src\listeners\lead\LeadRejectEventLogListener;
use src\listeners\lead\LeadSendToGaListener;
use src\listeners\lead\LeadSnoozeEventLogListener;
use src\listeners\lead\LeadSnoozeNotificationsListener;
use src\listeners\lead\LeadSoldClientReturnIndicationListener;
use src\listeners\lead\LeadSoldEventLogListener;
use src\listeners\lead\LeadSoldNotificationsListener;
use src\listeners\lead\LeadSoldSplitListener;
use src\listeners\lead\LeadInfoReloadListener;
use src\listeners\lead\LeadTaskEventListener;
use src\listeners\lead\LeadTaskListListener;
use src\listeners\lead\LeadTipsSplitListener;
use src\listeners\lead\LeadTrashEventLogListener;
use src\listeners\lead\LeadUserTaskCanceledListener;
use src\listeners\lead\leadWebEngage\LeadBookedWebEngageListener;
use src\listeners\lead\leadWebEngage\LeadSoldWebEngageListener;
use src\listeners\lead\leadWebEngage\LeadTrashedWebEngageListener;
use src\listeners\lead\leadBusinessExtraQueue\LeadBusinessExtraQueueRemoveOnStatusChangeListener;
use src\listeners\lead\leadBusinessExtraQueue\LeadBusinessExtraQueueEventLogListener;

return [
    LeadCreatedEvent::class => [
        LeadCreatedEventListener::class,
        LeadRatingCalculationListener::class,
    ],
    LeadCreatedManuallyEvent::class => [LeadSendToGaListener::class],
    LeadCreatedByIncomingCallEvent::class => [
        LeadCreatedByIncomingCallLogListener::class,
        LeadQcallAddListener::class,
        LeadSendToGaListener::class,
    ],
    LeadCreatedByApiEvent::class => [
        LeadCreatedByApiLogEventListener::class,
        LeadQcallAddListener::class,
    ],
    LeadCreatedByApiBOEvent::class => [
        LeadCreatedByApiBOLogEventListener::class,
        LeadQcallAddListener::class,
    ],
    LeadCreatedByIncomingSmsEvent::class => [
        LeadCreatedByIncomingSmsLogListener::class,
        LeadSendToGaListener::class,
    ],
    LeadCreatedByIncomingEmailEvent::class => [
        LeadCreatedByIncomingEmailLogListener::class,
        LeadSendToGaListener::class,
    ],
    LeadCreatedNewEvent::class => [
        LeadCreatedNewEventLogListener::class,
        LeadSendToGaListener::class,
    ],
    LeadCreatedClientChatEvent::class => [],

    LeadDuplicateDetectedEvent::class => [LeadDuplicateDetectedEventListener::class],
    LeadOwnerChangedEvent::class => [
        LeadTaskListListener::class,
        LeadOwnerChangedNotificationsListener::class,
        LeadPoorProcessingRemoverOwnerChangedListener::class,
        LeadInfoReloadListener::class,
    ],
    LeadCallExpertRequestEvent::class => [LeadCallExpertRequestEventListener::class],
    LeadTaskEvent::class => [LeadTaskEventListener::class],
    LeadCountPassengersChangedEvent::class => [LeadCountPassengersChangedEventListener::class],
    LeadCreatedCloneByUserEvent::class => [
        LeadCreatedCloneByUserEventListener::class,
        LeadSendToGaListener::class,
    ],

    LeadStatusChangedEvent::class => [
        LeadUserTaskCanceledListener::class,
        LeadQcallProcessingListener::class,
        LeadFromSnoozeNotificationListener::class,
        LeadPoorProcessingRemoverListener::class,
        LeadBusinessExtraQueueRemoveOnStatusChangeListener::class,
    ],
    LeadPendingEvent::class => [LeadPendingEventLogListener::class],
    LeadProcessingEvent::class => [
        LeadProcessingEventLogListener::class,
        LeadInfoReloadListener::class,
    ],
    LeadRejectEvent::class => [
        LeadRejectEventLogListener::class,
        LeadRejectClientReturnIndicationListener::class
    ],
    LeadFollowUpEvent::class => [
        LeadFollowUpEventLogListener::class,
        LeadFollowUpNotificationsListener::class,
        NoAnswerProtocolListener::class,
    ],
    LeadSoldEvent::class => [
        LeadSoldEventLogListener::class,
        LeadSoldNotificationsListener::class,
        LeadSoldSplitListener::class,
        LeadTipsSplitListener::class,
        LeadPhoneTrustListener::class,
        LeadSoldWebEngageListener::class,
        LeadSoldClientReturnIndicationListener::class,
        NoAnswerProtocolCancelListener::class,
    ],
    LeadTrashEvent::class => [
        LeadTrashEventLogListener::class,
        LeadTrashedWebEngageListener::class,
    ],
    LeadBookedEvent::class => [
        LeadBookedEventLogListener::class,
        LeadBookedNotificationsListener::class,
        LeadPhoneTrustListener::class,
        LeadBookedWebEngageListener::class,
        NoAnswerProtocolCancelListener::class,
    ],
    LeadSnoozeEvent::class => [
        LeadSnoozeEventLogListener::class,
        LeadSnoozeNotificationsListener::class,
    ],
    LeadNewEvent::class => [LeadNewEventLogListener::class],

    LeadQuoteCloneEvent::class => [LeadQuoteCloneEventListener::class],
    LeadPreferencesUpdateCurrencyEvent::class => [LeadPreferencesUpdateCurrencyEventListener::class],

    LeadExtraQueueEvent::class => [
        LeadExtraQueueEventLogListener::class,
        LeadExtraQueueNotificationsListener::class,
        LeadInfoReloadListener::class,
    ],
    LeadBusinessExtraQueueEvent::class => [
        LeadBusinessExtraQueueNotificationsListener::class,
        LeadInfoReloadListener::class,
        LeadBusinessExtraQueueEventLogListener::class,
    ],
    LeadPoorProcessingEvent::class => [
        LeadPoorProcessingAdderListener::class,
        LeadInfoReloadListener::class,
    ],
    LeadCloseEvent::class => [
        LeadCloseListener::class
    ],
    LeadCallExpertChangedEvent::class => [
        LeadCallExpertChangedListener::class
    ],
    ShiftScheduleEventChangedEvent::class => [
        ShiftScheduleEventChangedListener::class
    ]
];
