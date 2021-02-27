<?php

use modules\order\src\entities\order\events\OrderCanceledEvent;
use modules\order\src\entities\order\events\OrderCompletedEvent;
use modules\order\src\entities\order\events\OrderPaymentPaidEvent;
use modules\order\src\entities\order\events\OrderPreparedEvent;
use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use modules\order\src\entities\order\events\OrderUserProfitUpdateProfitAmountEvent;
use modules\order\src\entities\order\events\UpdateOrderTipsUserProfitAmountEvent;
use modules\order\src\events\OrderProcessingEvent;
use modules\order\src\listeners\order\OrderChangeStatusLogListener;
use modules\order\src\listeners\order\OrderLogPaymentStatusListener;
use modules\order\src\listeners\order\SendCanceledEmailListener;
use modules\order\src\processManager\listeners\OrderPrepareOrderProcessingListener;
use modules\order\src\listeners\order\OrderRecalculateProfitAmountListener;
use modules\order\src\listeners\order\SendConfirmationEmailListener;
use modules\order\src\listeners\order\SendOrderDetailsAndReceiptListener;
use modules\order\src\listeners\orderTipsUserProfit\UpdateOrderTipsUserProfitAmountEventListener;
use modules\order\src\listeners\orderUserProfit\OrderUserProfitUpdateProfitAmountEventListener;
use modules\order\src\payment\listeners\OrderProcessPaymentChargeListener;
use modules\order\src\processManager\events;
use modules\order\src\processManager\listeners;

return [
    OrderRecalculateProfitAmountEvent::class => [OrderRecalculateProfitAmountListener::class],
    OrderUserProfitUpdateProfitAmountEvent::class => [OrderUserProfitUpdateProfitAmountEventListener::class],
    OrderProcessingEvent::class => [],

    //OrderProcessManagerEvents
    events\CreatedEvent::class => [
        listeners\StartBookingListener::class,
        listeners\LogCreatedListener::class,
    ],
    events\BookingFlightEvent::class => [
        listeners\BookingFlightListener::class,
        listeners\LogStatusListener::class,
    ],
    events\BookingOtherProductsEvent::class => [
        listeners\StartBookingOtherProductsListener::class,
        listeners\LogStatusListener::class,
    ],
    events\BookedEvent::class => [
        listeners\LogStatusListener::class,
        OrderPrepareOrderProcessingListener::class,
    ],
    OrderPreparedEvent::class => [
        OrderProcessPaymentChargeListener::class,
        OrderChangeStatusLogListener::class,
    ],
    OrderPaymentPaidEvent::class => [
        listeners\OrderProcessOrderCompleteListener::class,
        OrderLogPaymentStatusListener::class,
    ],
    OrderCompletedEvent::class => [
        OrderChangeStatusLogListener::class,
        SendConfirmationEmailListener::class,
        SendOrderDetailsAndReceiptListener::class,
    ],
    OrderCanceledEvent::class => [
        OrderChangeStatusLogListener::class,
        SendCanceledEmailListener::class,
    ],
];
