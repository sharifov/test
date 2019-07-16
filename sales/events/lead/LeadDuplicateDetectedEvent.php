<?php

namespace sales\events\lead;

use common\models\Lead;

class LeadDuplicateDetectEvent
{
    public $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

}