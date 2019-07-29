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

    /**
     * LeadSoldEvent constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
