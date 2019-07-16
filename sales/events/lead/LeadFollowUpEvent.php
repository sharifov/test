<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadSnoozeEvent
 * @property Lead $lead
 */
class LeadSnoozeEvent
{
    public $lead;

    /**
     * LeadSnoozeEvent constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
