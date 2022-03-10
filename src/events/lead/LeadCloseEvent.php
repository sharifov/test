<?php

namespace src\events\lead;

use common\models\Lead;

/**
 * Class LeadCloseEvent
 * @package src\events\lead
 *
 * @property Lead $lead
 * @property string|null $leadStatusReasonKey
 * @property string|null $reasonComment
 * @property int|null $oldStatus
 * @property int|null $creatorId
 */
class LeadCloseEvent
{
    public $lead;
    public $leadStatusReasonKey;
    public $reasonComment;
    public $oldStatus;
    public $creatorId;

    public function __construct(
        Lead $lead,
        ?string $leadStatusReasonKey,
        ?int $oldStatus,
        ?int $creatorId,
        ?string $reasonComment
    ) {
        $this->lead = $lead;
        $this->leadStatusReasonKey = $leadStatusReasonKey;
        $this->oldStatus = $oldStatus;
        $this->creatorId = $creatorId;
        $this->reasonComment = $reasonComment;
    }
}
