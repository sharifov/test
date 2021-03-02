<?php

use modules\order\src\entities\order\events\OrderCanceledEvent;
use modules\order\src\entities\order\events\OrderCompletedEvent;
use modules\order\src\entities\order\events\OrderPaymentPaidEvent;
use modules\order\src\entities\order\events\OrderPreparedEvent;
use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use modules\order\src\entities\order\events\OrderUserProfitUpdateProfitAmountEvent;
use modules\order\src\entities\order\events\UpdateOrderTipsUserProfitAmountEvent;
use modules\order\src\events\OrderProcessingEvent;
use modules\order\src\listeners\lead\LeadSoldListener;
use modules\order\src\listeners\order\OrderCanceledConfirmationListener;
use modules\order\src\listeners\order\OrderCanceledHybridNotificationListener;
use modules\order\src\listeners\order\OrderChangeStatusLogListener;
use modules\order\src\listeners\order\OrderCompletedConfirmationListener;
use modules\order\src\listeners\order\OrderCompletedHybridNotificationListener;
use modules\order\src\listeners\order\OrderLogPaymentStatusListener;
use modules\order\src\listeners\order\OrderProcessingConfirmationListener;
use modules\order\src\listeners\order\OrderProcessingHybridNotificationListener;
use modules\order\src\processManager\listeners\OrderPrepareOrderProcessingListener;
use modules\order\src\listeners\order\OrderRecalculateProfitAmountListener;
use modules\order\src\listeners\orderTipsUserProfit\UpdateOrderTipsUserProfitAmountEventListener;
use modules\order\src\listeners\orderUserProfit\OrderUserProfitUpdateProfitAmountEventListener;
use modules\order\src\payment\listeners\OrderProcessPaymentChargeListener;
use modules\order\src\processManager\events;
use modules\order\src\processManager\listeners;

return [
    OrderRecalculateProfitAmountEvent::class => [OrderRecalculateProfitAmountListener::class],
    OrderUserProfitUpdateProfitAmountEvent::class => [OrderUserProfitUpdateProfitAmountEventListener::class],
    OrderProcessingEvent::class => [
        OrderProcessingConfirmationListener::class,
        OrderProcessingHybridNotificationListener::class,
    ],

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
    events\CanceledEvent::class => [
        listeners\LogStatusListener::class,
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
        LeadSoldListener::class,
        OrderChangeStatusLogListener::class,
        OrderCompletedConfirmationListener::class,
        OrderCompletedHybridNotificationListener::class,
    ],
    OrderCanceledEvent::class => [
        OrderChangeStatusLogListener::class,
        OrderCanceledConfirmationListener::class,
        OrderCanceledHybridNotificationListener::class,
    ],
];
