<?php

namespace modules\webEngage\src\service\webEngageEventData\lead\eventData;

use modules\webEngage\settings\WebEngageDictionary;

/**
 * Class LeadEmailRepliedEventData
 *
 */
class LeadEmailRepliedEventData extends AbstractLeadEventData
{
    public function getData(): array
    {
        return [
            'anonymousId' => (string) $this->lead->client_id,
            'eventName' => WebEngageDictionary::EVENT_LEAD_EMAIL_REPLIED,
            'eventTime' => date('Y-m-d\TH:i:sO'),
            'eventData' => $this->getEventData(),
        ];
    }

    public function getEventData(): array
    {
        $result['lead_id'] = $this->lead->id;
        $result['datetime'] = date('Y-m-d H:i:s');
        $result['project_key'] = $this->lead->project->project_key ?? null;
        $result['department_key'] = $this->lead->lDep->dep_key ?? null;

        return $result;
    }
}
