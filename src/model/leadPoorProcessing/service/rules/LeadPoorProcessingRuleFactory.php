<?php

namespace src\model\leadPoorProcessing\service\rules;

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;

/**
 * Class LeadPoorProcessingRuleFactory
 */
class LeadPoorProcessingRuleFactory
{
    private int $leadId;
    private string $ruleKey;
    private ?string $description = null;

    public function __construct(int $leadId, string $ruleKey, ?string $description = null)
    {
        $this->leadId = $leadId;
        $this->ruleKey = $ruleKey;
        $this->description = $description;
    }

    public function create(): LeadPoorProcessingServiceInterface
    {
        switch ($this->ruleKey) {
            case LeadPoorProcessingDataDictionary::KEY_LAST_ACTION:
                return new LeadPoorProcessingLastAction($this->leadId, $this->ruleKey, $this->description);
            case LeadPoorProcessingDataDictionary::KEY_NO_ACTION:
                return new LeadPoorProcessingNoAction($this->leadId, $this->ruleKey, $this->description);
            case LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_TAKE:
                return new LeadPoorProcessingTake($this->leadId, $this->ruleKey, $this->description);
            case LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_MULTIPLE_UPD:
                return new LeadPoorProcessingMultipleUpdate($this->leadId, $this->ruleKey, $this->description);
            case LeadPoorProcessingDataDictionary::KEY_SCHEDULED_COMMUNICATION:
                return new LeadPoorProcessingScheduledCommunication($this->leadId, $this->ruleKey, $this->description);
            case LeadPoorProcessingDataDictionary::KEY_EXPERT_IDLE:
                return new LeadPoorProcessingExpertIdle($this->leadId, $this->ruleKey, $this->description);
        }
        throw new \RuntimeException('ruleKey (' . $this->ruleKey . ') unprocessed');
    }
}
