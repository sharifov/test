<?php

namespace modules\webEngage\src\service\webEngageEventData\lead;

use common\models\Lead;
use modules\webEngage\settings\WebEngageSettings;
use modules\webEngage\src\service\webEngageEventData\lead\eventData\LeadEventDataFactory;

/**
 * Class LeadEventService
 *
 * @property Lead $lead
 * @property string $eventName
 * @property WebEngageSettings $settings
 */
class LeadEventService
{
    private Lead $lead;
    private string $eventName;
    private WebEngageSettings $settings;

    /**
     * @param Lead $lead
     * @param string $eventName
     */
    public function __construct(Lead $lead, string $eventName)
    {
        $this->lead = $lead;
        $this->eventName = $eventName;
        $this->settings = new WebEngageSettings();
    }

    public function isSourceCIDChecked(): bool
    {
        if (empty($this->settings->sourceCIds())) {
            return true;
        }
        if (!$cid = $this->lead->source->cid ?? null) {
            return false;
        }
        return in_array($cid, $this->settings->sourceCIds(), false);
    }

    public function getData(): array
    {
        return [
            'userId' => $this->lead->client->uuid,
            'eventName' => $this->eventName,
            'eventTime' => date('Y-m-d\TH:i:sO'),
            'eventData' => (new LeadEventDataFactory($this->lead, $this->eventName))->create()->getEventData(),
        ];
    }
}
