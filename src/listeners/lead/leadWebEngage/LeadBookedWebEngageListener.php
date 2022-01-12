<?php

namespace src\listeners\lead\leadWebEngage;

use modules\webEngage\settings\WebEngageDictionary;

/**
 * Class LeadBookedWebEngageListener
 */
class LeadBookedWebEngageListener extends AbstractLeadWebEngageListener
{
    /**
     * @param string $eventName
     */
    public function __construct(string $eventName = WebEngageDictionary::EVENT_LEAD_BOOKED)
    {
        $this->eventName = $eventName;
    }
}
