<?php

namespace src\events\lead;

use common\models\Lead;

class LeadReceiveEmailEvent implements LeadableEventInterface
{
    protected Lead $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }
}
