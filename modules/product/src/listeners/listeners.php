<?php

use modules\product\src\entities\productQuote\events\ProductQuoteCloneCreatedEvent;
use modules\product\src\entities\productQuoteOption\events\ProductQuoteOptionCloneCreatedEvent;

return [
    ProductQuoteCloneCreatedEvent::class => [],
    ProductQuoteOptionCloneCreatedEvent::class => [],
];
