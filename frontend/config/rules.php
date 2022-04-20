<?php

return [
    /*[
        'pattern' => 'queue/<type:(inbox1|follow-up1|processing1|processing-all|booked1|trash1)>',
        'route' => 'lead/queue',
    ],*/
    [
        'pattern' => 'user/info/<id>',
        'route' => 'user/info',
    ],
    [
        'pattern' => 'leads/view/<id>',
        'route' => 'leads/view',
    ],
    [
        'pattern' => 'lead/view/<gid>',
        'route' => 'lead/view',
    ],

    [
        'pattern' => 'cases/info/<id>',
        'route' => 'cases/info',
    ],
    [
        'pattern' => 'cases/view/<gid>',
        'route' => 'cases/view',
    ],

    [
        'pattern' => 'cases/take/<gid>',
        'route' => 'cases/take',
    ],

    [
        'pattern' => 'cases/take-over/<gid>',
        'route' => 'cases/take-over',
    ],
    [
        'pattern' => 'cases/ajax-sale-list-edit-info/<caseId>/<caseSaleId>',
        'route' => 'cases/ajax-sale-list-edit-info',
    ],
    [
        'pattern' => 'cases/ajax-sync-with-back-office/<caseId>/<caseSaleId>',
        'route' => 'cases/ajax-sync-with-back-office'
    ],
    [
        'pattern' => 'cases/ajax-refresh-sale-info/<caseId>/<caseSaleId>',
        'route' => 'cases/ajax-refresh-sale-info'
    ],
    [
        'pattern' => 'call/record/<callSid>',
        'route' => 'call/record'
    ],
    [
        'pattern' => 'conference/record/<conferenceSid>',
        'route' => 'conference/record'
    ],
    [
        'pattern' => 'voice-mail-record/record/<callId>',
        'route' => 'voice-mail-record/record'
    ],

    [
        'pattern' => 'take/<gid>',
        'route' => 'lead/take',
    ],
    [
        'pattern' => 'lead/get-salary/<dateparam>',
        'route' => 'lead/get-salary',
    ],

    [
        'pattern' => 'queue/pending',
        'route' => 'lead/pending',
    ],

    [
        'pattern' => 'queue/sold',
        'route' => 'lead/sold',
    ],
    [
        'pattern' => 'queue/processing',
        'route' => 'lead/processing',
    ],
    [
        'pattern' => 'queue/follow-up',
        'route' => 'lead/follow-up',
    ],
    [
        'pattern' => 'queue/inbox',
        'route' => 'lead/inbox',
    ],
    [
        'pattern' => 'queue/failed-bookings',
        'route' => 'lead/failed-bookings',
    ],
    [
        'pattern' => 'queue/trash',
        'route' => 'lead/trash',
    ],
    [
        'pattern' => 'queue/duplicate',
        'route' => 'lead/duplicate',
    ],
    [
        'pattern' => 'queue/booked',
        'route' => 'lead/booked',
    ],
    [
        'pattern' => 'queue/new',
        'route' => 'lead/new',
    ],
    [
        'pattern' => 'queue/bonus',
        'route' => 'lead/bonus',
    ],
    [
        'pattern' => '/',
        'route' => 'site/index',
    ],
    [
        'pattern' => 'queue/alternative',
        'route' => 'lead/alternative',
    ],
    [
        'pattern' => 'queue/business-inbox',
        'route' => 'lead/business-inbox',
    ],

];
