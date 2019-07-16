<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadChangedStatusEvent
 * @property Lead $lead
 */
class LeadChangedStatusEvent
{
    public $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
