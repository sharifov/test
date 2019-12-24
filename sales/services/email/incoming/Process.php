<?php

namespace sales\services\email\incoming;

/**
 * Class Process
 *
 * @property int|null $leadId
 * @property int|null $caseId
 */
class Process
{
    public $leadId;
    public $caseId;

    /**
     * @param int|null $leadId
     * @param int|null $caseId
     */
    public function __construct(?int $leadId, ?int $caseId)
    {
        $this->leadId = $leadId;
        $this->caseId = $caseId;
    }
}
