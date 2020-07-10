<?php

namespace sales\events\quote;

use common\models\Quote;

/**
 * Class QuoteSendEvent
 *
 * @property Quote $quote
 */
class QuoteSendEvent
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
