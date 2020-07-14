<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCreatedCloneEvent
 *
 * @property Lead $lead
 */
class LeadCreatedCloneEvent implements LeadableEventInterface
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
