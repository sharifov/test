<?php

namespace modules\webEngage\src\service\webEngageEventData\lead\eventData;

use modules\webEngage\settings\WebEngageDictionary;

/**
 * Class LeadEmailRepliedEventData
 *
 */
class LeadEmailRepliedEventData extends AbstractLeadEventData
{
    public function getEventData(): array
    {
        $result['lead_id'] = $this->getLead()->id;
        $result['datetime'] = date('Y-m-d H:i:s');
        $result['project_key'] = $this->getLead()->project->project_key ?? null;
        $result['department_key'] = $this->getLead()->lDep->dep_key ?? null;

        return $result;
    }
}
