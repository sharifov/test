<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCreatedManuallyEvent
 *
 * @property Lead $lead
 */
class LeadCreatedManuallyEvent implements LeadableEventInterface
{
    public $lead;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }
}
