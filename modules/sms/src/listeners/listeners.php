<?php

use sales\events\sms\IncomingSmsCreatedByLeadTypeEvent;
use sales\events\sms\IncomingSmsCreatedByCaseTypeEvent;
use sales\events\sms\SmsCreatedEvent;
use sales\listeners\sms\IncomingSmsCreatedByLeadTypeNotificationListener;
use sales\listeners\sms\IncomingSmsCreatedByCaseTypeNotificationListener;
use sales\listeners\sms\SmsIncomingCaseNeedActionListener;
use sales\listeners\sms\SmsIncomingSocketNotificationListener;
use sales\services\sms\incoming\SmsIncomingEvent;

return [
    SmsCreatedEvent::class => [],
    SmsIncomingEvent::class => [
        SmsIncomingCaseNeedActionListener::class,
        SmsIncomingSocketNotificationListener::class,
    ],
    IncomingSmsCreatedByLeadTypeEvent::class => [IncomingSmsCreatedByLeadTypeNotificationListener::class],
    IncomingSmsCreatedByCaseTypeEvent::class => [IncomingSmsCreatedByCaseTypeNotificationListener::class],
];
