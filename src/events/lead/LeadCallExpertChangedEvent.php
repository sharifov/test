<?php

namespace src\events\lead;

class LeadCallExpertChangedEvent
{
    public int $leadId;

    /**
     * @param int $leadId
     */
    public function __construct(
        int $leadId
    ) {
        $this->leadId = $leadId;
    }
}
