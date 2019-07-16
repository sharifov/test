<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCountPassengersChangedEvent
 * @property Lead $lead
 */
class LeadCountPassengersChangedEvent
{
    public $lead;

    /**
     * LeadCountPassengersChangedEvent constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;

    }
}
