<?php

namespace src\events\quote;

use common\models\Quote;

class QuoteSaveEvent
{
    public Quote $quote;
    public ?string $cid = null;

    public function __construct(Quote $quote, ?string $cid = null)
    {
        $this->quote = $quote;
        $this->cid = $cid;
    }
}
