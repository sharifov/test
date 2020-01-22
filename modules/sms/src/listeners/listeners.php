<?php

use sales\events\sms\SmsCreatedByIncomingSalesEvent;
use sales\events\sms\SmsCreatedByIncomingSupportsEvent;
use sales\events\sms\SmsCreatedEvent;
use sales\listeners\sms\SmsCreatedByIncomingSalesNotificationListener;
use sales\listeners\sms\SmsCreatedByIncomingSupportNotificationListener;

return [
    SmsCreatedEvent::class => [],
    SmsCreatedByIncomingSalesEvent::class => [SmsCreatedByIncomingSalesNotificationListener::class],
    SmsCreatedByIncomingSupportsEvent::class => [SmsCreatedByIncomingSupportNotificationListener::class],
];
