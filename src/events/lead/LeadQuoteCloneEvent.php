<?php

namespace src\events\lead;

use common\models\Quote;

/**
 * Class LeadQuoteCloneEvent
 *
 * @property Quote $quote
 */
class LeadQuoteCloneEvent
{
    public $quote;

    /**
     * @param Quote $quote
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }
}
