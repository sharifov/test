<?php

namespace src\model\leadStatusReason;

use common\models\Lead;

/**
 * Class HandleReasonDto
 * @package src\model\leadStatusReason
 *
 * @property Lead $lead
 * @property string $leadStatusReasonKey
 * @property int|null $newLeadOwnerId
 * @property int|null $creatorId
 * @property string|null $reason
 */
class HandleReasonDto
{
    public Lead $lead;
    public string $leadStatusReasonKey = '';
    public ?int $newLeadOwnerId = null;
    public ?int $creatorId = null;
    public ?string $reason = '';
    public ?int $originId = null;

    public function __construct(
        Lead $lead,
        string $leadStatusReasonKey,
        ?int $newLeadOwnerId,
        ?int $creatorId,
        ?string $reason,
        ?int $originId = null
    ) {
        $this->lead = $lead;
        $this->leadStatusReasonKey = $leadStatusReasonKey;
        $this->newLeadOwnerId = $newLeadOwnerId;
        $this->creatorId = $creatorId;
        $this->reason = $reason;
        $this->originId = $originId;
    }
}
