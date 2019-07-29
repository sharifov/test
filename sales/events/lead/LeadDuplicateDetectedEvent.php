<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadDuplicateDetectedEvent
 * @property Lead $lead
 */
class LeadDuplicateDetectedEvent
{
    public $lead;

    /**
     * LeadDuplicateDetectedEvent constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

}