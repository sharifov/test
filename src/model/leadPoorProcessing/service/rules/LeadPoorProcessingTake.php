<?php

namespace src\model\leadPoorProcessing\service\rules;

/**
 * Class LeadPoorProcessingTake
 */
class LeadPoorProcessingTake extends AbstractLeadPoorProcessingService implements LeadPoorProcessingServiceInterface
{
    public function checkCondition(): bool
    {
        if (!$this->getRule()->isEnabled()) {
            throw new \RuntimeException('Rule (' . $this->getRule()->lppd_key . ') not enabled');
        }
        if (!$this->getLead()->isProcessing()) {
            throw new \RuntimeException('Lead (' . $this->getLead()->id . ') not in status "processing"');
        }
        return true;
    }
}
