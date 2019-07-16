<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadSoldEvent
 * @property Lead $lead
 */
class LeadSoldEvent
{
    public $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
