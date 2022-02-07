<?php

namespace src\model\leadPoorProcessing\service\rules;

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;

/**
 * Class LeadPoorProcessingRuleFactory
 */
class LeadPoorProcessingRuleFactory
{
    private string $ruleKey;
    private int $leadId;

    public function __construct(int $leadId, string $ruleKey)
    {
        $this->ruleKey = $ruleKey;
        $this->leadId = $leadId;
    }

    public function create(): LeadPoorProcessingServiceInterface
    {
        switch ($this->ruleKey) {
            case LeadPoorProcessingDataDictionary::KEY_LAST_ACTION:
                return new LeadPoorProcessingLastAction($this->leadId, $this->ruleKey);
            case LeadPoorProcessingDataDictionary::KEY_NO_ACTION:
                return new LeadPoorProcessingNoAction($this->leadId, $this->ruleKey);
            case LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_TAKE:
                return new LeadPoorProcessingTake($this->leadId, $this->ruleKey);
            case LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_MULTIPLE_UPD:
                return new LeadPoorProcessingMultipleUpdate($this->leadId, $this->ruleKey);
        }
        throw new \RuntimeException('ruleKey (' . $this->ruleKey . ') unprocessed');
    }
}
