<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCreatedEvent
 * @property Lead $lead
 */
class LeadCreatedEvent
{
    public $lead;

    /**
     * LeadCreatedEvent constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
