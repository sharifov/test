<?php

namespace src\model\quote\abac\dto;

use common\models\Lead;
use common\models\Quote;

class QuoteFlightExtraMarkupAbacDto extends QuoteFlightAbacDto
{
    public function __construct(Lead $lead, Quote $quote, bool $is_owner)
    {
        $this->lead_status_id = $lead->status;
        $this->is_owner  = $is_owner;
        parent::__construct($quote);
    }
}
