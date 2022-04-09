<?php

use modules\order\src\listeners\order\OrderPrepareListener;
use modules\order\src\processManager;
use modules\product\src\entities\product\events\ProductClientBudgetChangedEvent;
use modules\product\src\entities\product\events\ProductMarketPriceChangedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedChangeFlowEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteCloneCreatedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteDeclinedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteExpiredEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteRecalculateChildrenProfitAmountEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteRecalculateProfitAmountEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteReplaceEvent;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeAutoDecisionPendingEvent;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeDecisionConfirmEvent;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeDecisionModifyEvent;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeDecisionRefundEvent;
use modules\product\src\entities\productQuoteOption\events\ProductQuoteOptionCloneCreatedEvent;
use modules\product\src\listeners\productQuote\ProductQuoteBookedChangeFlowListener;
use modules\product\src\listeners\productQuote\ProductQuoteDeclinedEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteExpiredEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteRecalculateChildrenProfitAmountListener;
use modules\product\src\listeners\productQuote\ProductQuoteRecalculateProfitAmountListener;
use modules\product\src\listeners\productQuote\ProductQuoteUpdateLeadOfferListener;
use modules\product\src\listeners\productQuote\ProductQuoteUpdateLeadOrderListener;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteCanceledEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteErrorEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteInProgressEvent;
use modules\product\src\listeners\productQuote\ProductQuoteBookedEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteCanceledEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteErrorEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteInProgressEventListener;
use modules\product\src\listeners\ProductQuoteCloneListener;
use modules\product\src\listeners\ProductQuoteReplaceListener;
use src\model\user\entity\profit\event\UserProfitCalculateByOrderUserProfitEvent;
use src\model\user\entity\profit\listener\UserProfitCalculateByOrderUserProfitEventListener;
use modules\product\src\entities\productQuote\events\ProductQuoteStatusChangeEvent;
use modules\product\src\listeners\productQuote\ProductQuoteStatusChangeEventListener;

return [
    ProductQuoteCloneCreatedEvent::class => [
        ProductQuoteStatusChangeEventListener::class,
        ProductQuoteCloneListener::class,
    ],
    ProductQuoteReplaceEvent::class => [
        ProductQuoteReplaceListener::class,
    ],
    ProductQuoteOptionCloneCreatedEvent::class => [],
    ProductQuoteInProgressEvent::class => [
        ProductQuoteInProgressEventListener::class,
        ProductQuoteUpdateLeadOrderListener::class,
        ProductQuoteUpdateLeadOfferListener::class,
    ],
    ProductQuoteBookedEvent::class => [
        ProductQuoteBookedEventListener::class,
        processManager\phoneToBook\listeners\AfterBookedFlightOrderProcessListener::class,
        processManager\phoneToBook\listeners\OrderProcessManagerBookingListener::class,
        processManager\clickToBook\listeners\AllProductsBookedListener::class,
        OrderPrepareListener::class,
        ProductQuoteUpdateLeadOrderListener::class,
        ProductQuoteUpdateLeadOfferListener::class,
    ],
    ProductQuoteErrorEvent::class => [
        ProductQuoteErrorEventListener::class,
        ProductQuoteUpdateLeadOrderListener::class,
        ProductQuoteUpdateLeadOfferListener::class,
    ],
    ProductQuoteCanceledEvent::class => [
        ProductQuoteCanceledEventListener::class,
        ProductQuoteUpdateLeadOrderListener::class,
        ProductQuoteUpdateLeadOfferListener::class,
    ],
    ProductQuoteDeclinedEvent::class => [
        ProductQuoteDeclinedEventListener::class,
        ProductQuoteUpdateLeadOrderListener::class,
        ProductQuoteUpdateLeadOfferListener::class,
    ],
    ProductQuoteExpiredEvent::class => [
        ProductQuoteExpiredEventListener::class,
        ProductQuoteUpdateLeadOrderListener::class,
        ProductQuoteUpdateLeadOfferListener::class,
    ],
    ProductQuoteRecalculateProfitAmountEvent::class => [ProductQuoteRecalculateProfitAmountListener::class],
    ProductQuoteRecalculateChildrenProfitAmountEvent::class => [ProductQuoteRecalculateChildrenProfitAmountListener::class],
    UserProfitCalculateByOrderUserProfitEvent::class => [UserProfitCalculateByOrderUserProfitEventListener::class],
    ProductMarketPriceChangedEvent::class => [],
    ProductClientBudgetChangedEvent::class => [],

    ProductQuoteChangeAutoDecisionPendingEvent::class => [
        \src\model\client\notifications\listeners\productQuoteChangeAutoDecisionPending\ClientNotificationListener::class,
    ],

    ProductQuoteChangeDecisionConfirmEvent::class => [
        \src\model\client\notifications\listeners\productQuoteChangeDecided\ClientNotificationCancelerListener::class,
    ],

    ProductQuoteChangeDecisionRefundEvent::class => [
        \src\model\client\notifications\listeners\productQuoteChangeDecided\ClientNotificationCancelerListener::class,
    ],

    ProductQuoteChangeDecisionModifyEvent::class => [
        \src\model\client\notifications\listeners\productQuoteChangeDecided\ClientNotificationCancelerListener::class,
    ],
    ProductQuoteBookedChangeFlowEvent::class => [
        ProductQuoteBookedChangeFlowListener::class
    ],
    ProductQuoteStatusChangeEvent::class => [
        ProductQuoteStatusChangeEventListener::class
    ],
];
