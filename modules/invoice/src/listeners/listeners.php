<?php

use modules\invoice\src\entities\invoice\events\InvoicePaidEvent;
use modules\invoice\src\listeners\AllInvoicePaidListener;
use modules\invoice\src\listeners\InvoiceStatusReloadLeadOrdersListener;
use modules\invoice\src\listeners\PaidInvoiceListener;
use modules\order\src\payment\events\PaymentCompletedEvent;
use modules\order\src\payment\listeners\PaymentStatusReloadLeadOrdersListener;

return [
    PaymentCompletedEvent::class => [
        PaidInvoiceListener::class,
        PaymentStatusReloadLeadOrdersListener::class,
    ],
    InvoicePaidEvent::class => [
        AllInvoicePaidListener::class,
        InvoiceStatusReloadLeadOrdersListener::class,
    ]
];
