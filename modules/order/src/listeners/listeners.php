<?php

use modules\order\src\events\OrderFileGeneratedEvent;
use modules\order\src\events\OrderProcessingEvent;
use modules\order\src\entities\order\events as OrderEvents;
use modules\order\src\listeners\lead as LeadListeners;
use modules\order\src\listeners\order as OrderListeners;
use modules\order\src\processManager;
use modules\order\src\listeners\orderUserProfit\OrderUserProfitUpdateProfitAmountEventListener;

return [
    OrderEvents\OrderRecalculateProfitAmountEvent::class => [
        OrderListeners\OrderRecalculateProfitAmountListener::class,
    ],
    OrderEvents\OrderUserProfitUpdateProfitAmountEvent::class => [
        OrderUserProfitUpdateProfitAmountEventListener::class,
    ],
    OrderProcessingEvent::class => [
        OrderListeners\OrderProcessingConfirmationListener::class,
        OrderListeners\OrderProcessingHybridNotificationListener::class,
        processManager\phoneToBook\listeners\StartAutoProcessingListener::class,
        OrderListeners\OrderChangeStatusLogListener::class,
    ],
    OrderEvents\OrderCancelProcessingEvent::class => [
        OrderListeners\OrderChangeStatusLogListener::class,
    ],
    OrderEvents\OrderDeclinedEvent::class => [
        OrderListeners\OrderChangeStatusLogListener::class,
    ],
    OrderEvents\OrderErrorEvent::class => [
        OrderListeners\OrderChangeStatusLogListener::class,
    ],
    OrderEvents\OrderNewEvent::class => [
        OrderListeners\OrderChangeStatusLogListener::class,
    ],
    OrderEvents\OrderPendingEvent::class => [
        OrderListeners\OrderChangeStatusLogListener::class,
    ],
    OrderEvents\OrderRecalculateTotalPriceEvent::class => [
        OrderListeners\OrderRecalculateTotalPriceListener::class
    ],
    OrderEvents\OrderPreparedEvent::class => [
        processManager\phoneToBook\listeners\OrderProcessPaymentChargeListener::class,
        OrderListeners\OrderChangeStatusLogListener::class,
        LeadListeners\LeadStatusReloadOrdersListener::class,
    ],
    OrderEvents\OrderPaymentPaidEvent::class => [
        processManager\phoneToBook\listeners\OrderProcessOrderCompleteListener::class,
        OrderListeners\OrderLogPaymentStatusListener::class,
        LeadListeners\LeadPaymentStatusReloadOrdersListener::class,
    ],
    OrderEvents\OrderCompletedEvent::class => [
        LeadListeners\LeadSoldListener::class,
        OrderListeners\OrderChangeStatusLogListener::class,
        OrderListeners\OrderCompletedHybridNotificationListener::class,
        OrderListeners\OrderFileGeneratorListener::class,
        LeadListeners\LeadStatusReloadOrdersListener::class,
    ],
    OrderEvents\OrderCanceledEvent::class => [
        OrderListeners\OrderChangeStatusLogListener::class,
//        OrderListeners\OrderCanceledConfirmationListener::class,
        OrderListeners\OrderCanceledHybridNotificationListener::class,
        LeadListeners\LeadStatusReloadOrdersListener::class,
    ],

    OrderFileGeneratedEvent::class => [
        OrderListeners\OrderAllFilesGeneratedListener::class,
    ],

    processManager\events\CreatedEvent::class => [
        processManager\phoneToBook\listeners\StartBookingListener::class,
        processManager\listeners\LogCreatedListener::class,
        processManager\listeners\OrderProcessStatusReloadLeadOrdersListener::class,
    ],
    processManager\events\BookingFlightEvent::class => [
        processManager\phoneToBook\listeners\BookingFlightListener::class,
        processManager\listeners\LogStatusListener::class,
        processManager\listeners\OrderProcessStatusReloadLeadOrdersListener::class,
    ],
    processManager\events\BookingOtherProductsEvent::class => [
        processManager\phoneToBook\listeners\StartBookingOtherProductsListener::class,
        processManager\listeners\LogStatusListener::class,
        processManager\listeners\OrderProcessStatusReloadLeadOrdersListener::class,
    ],
    processManager\events\BookedEvent::class => [
        processManager\phoneToBook\listeners\OrderPrepareOrderProcessingListener::class,
        processManager\listeners\OrderProcessStatusReloadLeadOrdersListener::class,
        processManager\listeners\LogStatusListener::class,
    ],
    processManager\events\CanceledEvent::class => [
        processManager\listeners\LogStatusListener::class,
        processManager\listeners\OrderProcessStatusReloadLeadOrdersListener::class,
    ],
    processManager\events\FailedEvent::class => [
        processManager\listeners\LogStatusListener::class,
        processManager\listeners\OrderProcessStatusReloadLeadOrdersListener::class,
    ],
];
