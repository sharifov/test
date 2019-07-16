<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadBookedEvent
 * @property Lead $lead
 */
class LeadBookedEvent
{
    public $lead;

    /**
     * LeadBookedEvent constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
