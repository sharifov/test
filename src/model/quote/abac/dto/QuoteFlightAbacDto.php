<?php

namespace src\model\quote\abac\dto;

use common\models\Quote;

class QuoteFlightAbacDto extends \stdClass
{
    public int $lead_status_id = 0;
    public bool $is_owner = false;
    public int $quote_status_id;


    public function __construct(Quote $quote)
    {
        $this->quote_status_id = $quote->status;
    }
}
