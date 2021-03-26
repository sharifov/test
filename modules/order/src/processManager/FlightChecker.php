<?php

namespace modules\order\src\processManager;

use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class FlightChecker
 */
class FlightChecker
{
    public function has(int $orderId): bool
    {
        $quotes = ProductQuote::find()->byOrderId($orderId)->all();
        if (!$quotes) {
            return false;
        }
        foreach ($quotes as $quote) {
            if ($quote->pqProduct->isFlight()) {
                return true;
            }
        }
        return false;
    }
}
