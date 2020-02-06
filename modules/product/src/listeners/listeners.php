<?php

use modules\product\src\entities\productQuote\events\ProductQuoteCloneCreatedEvent;
use modules\product\src\entities\productQuoteOption\events\ProductQuoteOptionCloneCreatedEvent;
use modules\product\src\listeners\ProductQuoteChangeStatusLogListener;

return [
    ProductQuoteCloneCreatedEvent::class => [ProductQuoteChangeStatusLogListener::class],
    ProductQuoteOptionCloneCreatedEvent::class => [],
];
