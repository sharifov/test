<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadPassengersChangedEvent
 * @property Lead $lead
 */
class LeadPassengersChangedEvent
{
    public $lead;

    /**
     * LeadPassengersChangedEvent constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;

    }
}
