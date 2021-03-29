<?php

namespace modules\order\src\processManager;

use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class AllProductsBookedChecker
 */
class AllProductsBookedChecker
{
    public function isBooked(int $orderId): bool
    {
        $quotes = ProductQuote::find()->byOrderId($orderId)->all();

        if (!$quotes) {
            return false;
        }

        $isBooked = false;

        foreach ($quotes as $quote) {
            if ($quote->isBooked()) {
                $isBooked = true;
            }
            if ($quote->isError()) {
                return false;
            }
            if ($quote->isApplied()) {
                return false;
            }
        }

        return $isBooked;
    }
}
