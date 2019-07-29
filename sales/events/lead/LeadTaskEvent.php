<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadTaskEvent
 * @property Lead $lead
 */
class LeadTaskEvent
{
    public $lead;

    /**
     * LeadTaskEvent constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
