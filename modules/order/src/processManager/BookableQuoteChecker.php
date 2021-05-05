<?php

namespace modules\order\src\processManager;

use modules\product\src\entities\productQuote\ProductQuote;

class BookableQuoteChecker
{
    public function has(int $orderId): bool
    {
        return ProductQuote::find()->byOrderId($orderId)->applied()->exists();
    }
}
