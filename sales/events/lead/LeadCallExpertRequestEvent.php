<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCallExpertRequestEvent
 * @property Lead $lead
 */
class LeadCallExpertRequestEvent
{
    public $lead;

    /**
     * LeadCallExpertRequestEvent constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
