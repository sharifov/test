<?php

use sales\events\sms\SmsCreatedByIncomingSalesEvent;
use sales\events\sms\SmsCreatedByIncomingSupportsEvent;
use sales\events\sms\SmsCreatedEvent;
use sales\listeners\sms\SmsCreatedByIncomingSalesNotificationListener;
use sales\listeners\sms\SmsCreatedByIncomingSupportNotificationListener;
use sales\listeners\sms\SmsIncomingCaseNeedActionListener;
use sales\services\sms\incoming\SmsIncomingEvent;

return [
    SmsCreatedEvent::class => [],
    SmsIncomingEvent::class => [SmsIncomingCaseNeedActionListener::class],
    SmsCreatedByIncomingSalesEvent::class => [SmsCreatedByIncomingSalesNotificationListener::class],
    SmsCreatedByIncomingSupportsEvent::class => [SmsCreatedByIncomingSupportNotificationListener::class],
];
