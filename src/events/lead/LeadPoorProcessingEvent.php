<?php

namespace src\events\lead;

use common\models\Lead;

/**
 * Class LeadPoorProcessingEvent
 *
 */
class LeadPoorProcessingEvent implements LeadableEventInterface
{
    private Lead $lead;
    private array $dataKeys;
    private ?string $description = null;

    public function __construct(Lead $lead, array $dataKeys, ?string $description = null)
    {
        $this->lead = $lead;
        $this->dataKeys = $dataKeys;
        $this->description = $description;
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }

    public function getDataKeys(): array
    {
        return $this->dataKeys;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
