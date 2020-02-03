<?php

use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteErrorEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteInProgressEvent;
use modules\product\src\listeners\productQuote\ProductQuoteBookedEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteErrorEventListener;
use modules\product\src\listeners\productQuote\ProductQuoteInProgressEventListener;

return [
    ProductQuoteInProgressEvent::class => [ProductQuoteInProgressEventListener::class],
    ProductQuoteBookedEvent::class => [ProductQuoteBookedEventListener::class],
    ProductQuoteErrorEvent::class => [ProductQuoteErrorEventListener::class],
];
