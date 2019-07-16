<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCreatedCloneEvent
 * @property Lead $lead
 */
class LeadCreatedCloneEvent
{
    public $lead;

    /**
     * LeadCreatedCloneEvent constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
