<?php

namespace src\events\lead;

use common\models\Lead;

/**
 * Class LeadPoorProcessingEvent
 *
 */
class LeadPoorProcessingEvent
{
    private Lead $lead;
    private string $dataKey;

    public function __construct(Lead $lead, string $dataKey)
    {
        $this->lead = $lead;
        $this->dataKey = $dataKey;
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }

    public function getDataKey(): string
    {
        return $this->dataKey;
    }
}
