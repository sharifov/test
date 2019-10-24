<?php

namespace sales\repositories\quote;

use common\models\QuotePrice;

/**
 * Class QuotePriceRepository
 */
class QuotePriceRepository
{
    
    /**
     * @param QuotePrice $quotePrice
     * @return int
     */
    public function save(QuotePrice $quotePrice): int
    {
        if (!$quotePrice->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $quotePrice->id;
    }
    
}
