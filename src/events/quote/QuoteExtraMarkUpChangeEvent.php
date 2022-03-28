<?php

namespace src\events\quote;

use common\models\Quote;

/**
 *
 */
class QuoteExtraMarkUpChangeEvent
{
    public Quote $quote;
    public ?int $userId;
    public ?float $sellingOld;

    /**
     * @param Quote $quote
     */
    public function __construct(Quote $quote, ?int $userId = null, ?float $sellingOld = null)
    {
        $this->quote = $quote;
        $this->userId = $userId;
        $this->sellingOld = $sellingOld;
    }
}
