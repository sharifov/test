<?php

use src\events\sms\IncomingSmsCreatedByLeadTypeEvent;
use src\events\sms\IncomingSmsCreatedByCaseTypeEvent;
use src\events\sms\SmsCreatedEvent;
use src\listeners\sms\IncomingSmsCreatedByLeadTypeNotificationListener;
use src\listeners\sms\IncomingSmsCreatedByCaseTypeNotificationListener;
use src\listeners\sms\SmsIncomingCaseNeedActionListener;
use src\listeners\sms\SmsIncomingSocketNotificationListener;
use src\services\sms\incoming\SmsIncomingEvent;

return [
    SmsCreatedEvent::class => [],
    SmsIncomingEvent::class => [
        SmsIncomingCaseNeedActionListener::class,
        SmsIncomingSocketNotificationListener::class,
    ],
    IncomingSmsCreatedByLeadTypeEvent::class => [IncomingSmsCreatedByLeadTypeNotificationListener::class],
    IncomingSmsCreatedByCaseTypeEvent::class => [IncomingSmsCreatedByCaseTypeNotificationListener::class],
];
