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
    private ?string $description = null;

    public function __construct(Lead $lead, string $dataKey, ?string $description = null)
    {
        $this->lead = $lead;
        $this->dataKey = $dataKey;
        $this->description = $description;
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }

    public function getDataKey(): string
    {
        return $this->dataKey;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
