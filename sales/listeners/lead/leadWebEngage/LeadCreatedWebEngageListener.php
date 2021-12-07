<?php

namespace sales\listeners\lead\leadWebEngage;

use modules\webEngage\settings\WebEngageDictionary;

/**
 * Class LeadCreatedWebEngageListener
 */
class LeadCreatedWebEngageListener extends AbstractLeadWebEngageListener
{
    /**
     * @param string $eventName
     */
    public function __construct(string $eventName = WebEngageDictionary::EVENT_LEAD_CREATED)
    {
        $this->eventName = $eventName;
    }
}
