<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCreatedManuallyEvent
 *
 * @property Lead $lead
 */
class LeadCreatedManuallyEvent
{
    public $lead;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

}
