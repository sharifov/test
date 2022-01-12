<?php

namespace src\listeners\lead\leadWebEngage;

use modules\webEngage\settings\WebEngageDictionary;

/**
 * Class LeadSoldWebEngageListener
 */
class LeadSoldWebEngageListener extends AbstractLeadWebEngageListener
{
    /**
     * @param string $eventName
     */
    public function __construct(string $eventName = WebEngageDictionary::EVENT_LEAD_SOLD)
    {
        $this->eventName = $eventName;
    }
}
