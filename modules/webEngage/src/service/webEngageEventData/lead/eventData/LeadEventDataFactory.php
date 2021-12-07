<?php

namespace modules\webEngage\src\service\webEngageEventData\lead\eventData;

use common\models\Lead;
use modules\webEngage\settings\WebEngageDictionary;

/**
 * Class LeadEventDataFactory
 *
 * @property Lead $lead
 * @property string $eventName
 */
class LeadEventDataFactory
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

    public function create(): AbstractLeadEventData
    {
        switch ($this->eventName) {
            case WebEngageDictionary::EVENT_LEAD_CREATED:
                return new LeadCreatedEventData($this->lead);
            case WebEngageDictionary::EVENT_LEAD_BOOKED:
                return new LeadBookedEventData($this->lead);
            case WebEngageDictionary::EVENT_LEAD_SOLD:
                return new LeadSoldEventData($this->lead);
            case WebEngageDictionary::EVENT_LEAD_TRASHED:
                return new LeadTrashedEventData($this->lead);
            case WebEngageDictionary::EVENT_LEAD_EMAIL_REPLIED:
                return new LeadEmailRepliedEventData($this->lead);
        }
        throw new \RuntimeException('EventName (' . $this->eventName . ') unprocessed');
    }

    public function createByStatus(): AbstractLeadEventData
    {
        switch ($this->lead->status) {
            case Lead::STATUS_NEW:
                return new LeadCreatedEventData($this->lead);
            case Lead::STATUS_BOOKED:
                return new LeadBookedEventData($this->lead);
            case Lead::STATUS_SOLD:
                return new LeadSoldEventData($this->lead);
            case Lead::STATUS_TRASH:
                return new LeadTrashedEventData($this->lead);
        }
        throw new \RuntimeException('Lead Status (' . $this->lead->getStatusName() . ') unprocessed');
    }
}
