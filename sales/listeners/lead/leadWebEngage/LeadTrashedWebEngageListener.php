<?php

namespace sales\listeners\lead\leadWebEngage;

use modules\webEngage\settings\WebEngageDictionary;

/**
 * Class LeadBookedWebEngageListener
 */
class LeadTrashedWebEngageListener extends AbstractLeadWebEngageListener
{
    /**
     * @param string $eventName
     */
    public function __construct(string $eventName = WebEngageDictionary::EVENT_LEAD_TRASHED)
    {
        $this->eventName = $eventName;
    }
}
