<?php

namespace src\model\leadPoorProcessing\service\rules;

/**
 * Class LeadPoorProcessingLastAction
 */
class LeadPoorProcessingLastAction extends AbstractLeadPoorProcessingService implements LeadPoorProcessingServiceInterface
{
    public function checkCondition(): bool
    {
        return $this->getLead()->isProcessing() && $this->getLead()->hasOwner() && $this->getRule()->isEnabled();
    }
}
