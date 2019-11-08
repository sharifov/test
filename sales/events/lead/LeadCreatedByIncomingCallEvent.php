<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCreatedByIncomingCallEvent
 *
 * @property Lead $lead
 */
class LeadCreatedByIncomingCallEvent
{
    public $lead;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
