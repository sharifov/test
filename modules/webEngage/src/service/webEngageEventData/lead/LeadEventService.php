<?php

namespace modules\webEngage\src\service\webEngageEventData\lead;

use common\models\Lead;
use modules\webEngage\src\service\webEngageEventData\lead\eventData\LeadEventDataFactory;

/**
 * Class LeadEventService
 *
 * @property Lead $lead
 * @property string $eventName
 */
class LeadEventService
{
    private Lead $lead;
    private string $eventName;

    /**
     * @param Lead $lead
     * @param string $eventName
     */
    public function __construct(Lead $lead, string $eventName)
    {
        $this->lead = $lead;
        $this->eventName = $eventName;
    }

    public function getData(): array
    {
        return [
            'anonymousId' => (string) $this->lead->client_id,
            'eventName' => $this->eventName,
            'eventTime' => date('Y-m-d\TH:i:sO'),
            'eventData' => (new LeadEventDataFactory($this->lead, $this->eventName))->create()->getEventData(),
        ];
    }
}
