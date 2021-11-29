<?php

namespace modules\webEngage\src\service\webEngageEventData\lead;

use common\models\Lead;

/**
 * Class LeadEventService
 *
 * @property Lead $lead
 */
class LeadEventService
{
    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
        if (!in_array($this->lead->status, LeadEventDictionary::STATUS_PROCESSED_LIST, false)) {
            throw new \RuntimeException('Lead Status (' . $this->lead->getStatusName() . ') unprocessed');
        }
    }

    public function getData(): array
    {
        return [
            'anonymousId' => (string) $this->lead->client_id,
            'eventName' => LeadEventDictionary::getEventNameByStatus($this->lead->status),
            'eventTime' => date('Y-m-d\TH:i:sO'),
            'eventData' => (new LeadEventDataFactory($this->lead))->create()->getEventData(),
        ];
    }

    public static function findLead(?int $leadId, array $statuses = LeadEventDictionary::STATUS_PROCESSED_LIST): ?Lead
    {
        return Lead::find()
            ->where(['id' => $leadId])
            ->andWhere(['IN', 'status', $statuses])
            ->one();
    }
}
