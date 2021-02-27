<?php

use modules\invoice\src\entities\invoice\events\InvoicePaidEvent;
use modules\invoice\src\listeners\AllInvoicePaidListener;
use modules\invoice\src\listeners\PaidInvoiceListener;
use modules\order\src\payment\events\PaymentCompletedEvent;

return [
    PaymentCompletedEvent::class => [
        PaidInvoiceListener::class,
    ],
    InvoicePaidEvent::class => [
        AllInvoicePaidListener::class,
    ]
];
