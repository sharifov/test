<?php

use modules\order\src\listeners\order\OrderPrepareListener;
use modules\order\src\processManager\phoneToBook\listeners\AfterBookedFlightOrderProcessListener;
use modules\order\src\processManager\phoneToBook\listeners\AfterBookedQuoteOrderProcessListener;
use modules\product\src\entities\product\events\ProductClientBudgetChangedEvent;
use modules\product\src\entities\product\events\ProductMarketPriceChangedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteCloneCreatedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteDeclinedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteExpiredEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteRecalculateChildrenProfitAmountEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteRecalculateProfitAmountEvent;
use modules\product\src\entities\productQuoteOption\events\ProductQuoteOptionCloneCreatedEvent;
use modules\product\src\listeners\productQuote\ProductQuoteDeclinedEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteExpiredEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteRecalculateChildrenProfitAmountListener;
use modules\product\src\listeners\productQuote\ProductQuoteRecalculateProfitAmountListener;
use modules\product\src\listeners\productQuote\ProductQuoteUpdateLeadOfferListener;
use modules\product\src\listeners\productQuote\ProductQuoteUpdateLeadOrderListener;
use modules\product\src\listeners\ProductQuoteChangeStatusLogListener;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteCanceledEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteErrorEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteInProgressEvent;
use modules\product\src\listeners\productQuote\ProductQuoteBookedEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteCanceledEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteErrorEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteInProgressEventListener;
use sales\model\user\entity\profit\event\UserProfitCalculateByOrderUserProfitEvent;
use sales\model\user\entity\profit\listener\UserProfitCalculateByOrderUserProfitEventListener;

return [
    ProductQuoteCloneCreatedEvent::class => [ProductQuoteChangeStatusLogListener::class],
    ProductQuoteOptionCloneCreatedEvent::class => [],
    ProductQuoteInProgressEvent::class => [
        ProductQuoteInProgressEventListener::class,
        ProductQuoteUpdateLeadOrderListener::class,
        ProductQuoteUpdateLeadOfferListener::class,
    ],
    ProductQuoteBookedEvent::class => [
        ProductQuoteBookedEventListener::class,
        AfterBookedFlightOrderProcessListener::class,
        AfterBookedQuoteOrderProcessListener::class,
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
];
