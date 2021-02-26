<?php

use modules\order\src\entities\order\events\OrderCompleteEvent;
use modules\order\src\entities\order\events\OrderPaymentPaidEvent;
use modules\order\src\entities\order\events\OrderPreparedEvent;
use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use modules\order\src\entities\order\events\OrderUserProfitUpdateProfitAmountEvent;
use modules\order\src\entities\order\events\UpdateOrderTipsUserProfitAmountEvent;
use modules\order\src\events\OrderProcessingEvent;
use modules\order\src\listeners\order\OrderCompleteListener;
use modules\order\src\listeners\order\OrderLogPaymentStatusListener;
use modules\order\src\listeners\order\OrderLogStatusListener;
use modules\order\src\listeners\order\OrderPrepareListener;
use modules\order\src\listeners\order\OrderRecalculateProfitAmountListener;
use modules\order\src\listeners\order\SendConfirmationEmailListener;
use modules\order\src\listeners\order\SendOrderDetailsAndReceiptListener;
use modules\order\src\listeners\orderTipsUserProfit\UpdateOrderTipsUserProfitAmountEventListener;
use modules\order\src\listeners\orderUserProfit\OrderUserProfitUpdateProfitAmountEventListener;
use modules\order\src\payment\listeners\PaymentChargeListener;
use modules\order\src\processManager\events;
use modules\order\src\processManager\listeners;

return [
    OrderRecalculateProfitAmountEvent::class => [OrderRecalculateProfitAmountListener::class],
    OrderUserProfitUpdateProfitAmountEvent::class => [OrderUserProfitUpdateProfitAmountEventListener::class],
    OrderProcessingEvent::class => []

    //OrderProcessManagerEvents
    events\CreatedEvent::class => [
        listeners\StartBookingListener::class,
        listeners\LogCreatedListener::class,
    ],
    events\BookingFlightEvent::class => [
        listeners\BookingFlightListener::class,
        listeners\LogStatusListener::class,
    ],
    events\FlightQuoteBookedEvent::class => [
        listeners\AfterBookedFlightListener::class,
    ],
    events\BookingOtherProductsEvent::class => [
        listeners\StartBookingOtherProductsListener::class,
        listeners\LogStatusListener::class,
    ],
    events\QuoteBookedEvent::class => [
        listeners\AfterBookedQuoteListener::class,
    ],
    events\BookedEvent::class => [
        listeners\LogStatusListener::class,
        OrderPrepareListener::class,
    ],
    OrderPreparedEvent::class => [
        PaymentChargeListener::class,
        OrderLogStatusListener::class,
    ],
    OrderPaymentPaidEvent::class => [
        OrderCompleteListener::class,
        OrderLogPaymentStatusListener::class,
    ],
    OrderCompleteEvent::class => [
        OrderLogStatusListener::class,
        SendConfirmationEmailListener::class,
        SendOrderDetailsAndReceiptListener::class,
    ],
>>>>>>> feat: added order processing flow (SL-4925)
];
