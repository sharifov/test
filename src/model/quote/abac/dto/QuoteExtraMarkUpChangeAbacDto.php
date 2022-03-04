<?php

namespace src\model\quote\abac\dto;

use common\models\Lead;
use common\models\Quote;
use src\auth\Auth;

class QuoteExtraMarkUpChangeAbacDto extends \stdClass
{
    public int $lead_status_id;
    public bool $is_owner;
    public int $quote_status_id;


    public function __construct(Lead $lead, Quote $quote)
    {
        $this->lead_status_id = $lead->status;
        $this->quote_status_id = $quote->status;
        $this->is_owner  = $lead->employee_id === Auth::id();
    }
}
