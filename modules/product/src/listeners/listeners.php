<?php

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
use modules\product\src\listeners\ProductQuoteChangeStatusLogListener;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteCanceledEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteErrorEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteInProgressEvent;
use modules\product\src\listeners\productQuote\ProductQuoteBookedEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteCanceledEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteErrorEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteInProgressEventListener;

return [
    ProductQuoteCloneCreatedEvent::class => [ProductQuoteChangeStatusLogListener::class],
    ProductQuoteOptionCloneCreatedEvent::class => [],
    ProductQuoteInProgressEvent::class => [ProductQuoteInProgressEventListener::class],
    ProductQuoteBookedEvent::class => [ProductQuoteBookedEventListener::class],
    ProductQuoteErrorEvent::class => [ProductQuoteErrorEventListener::class],
    ProductQuoteCanceledEvent::class => [ProductQuoteCanceledEventListener::class],
    ProductQuoteDeclinedEvent::class => [ProductQuoteDeclinedEventListener::class],
    ProductQuoteExpiredEvent::class => [ProductQuoteExpiredEventListener::class],
    ProductQuoteRecalculateProfitAmountEvent::class => [ProductQuoteRecalculateProfitAmountListener::class],
    ProductQuoteRecalculateChildrenProfitAmountEvent::class => [ProductQuoteRecalculateChildrenProfitAmountListener::class],
];
