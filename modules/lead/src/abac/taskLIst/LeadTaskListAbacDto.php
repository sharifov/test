<?php

namespace modules\lead\src\abac\taskLIst;

use common\models\Lead;
use modules\lead\src\services\LeadTaskListService;

/**
 * Class LeadTaskListAbacDto
 */
class LeadTaskListAbacDto extends \stdClass
{
    public ?bool $is_owner = null;
    public ?bool $has_owner = null;
    public ?int $statusId = null;
    public ?int $projectId = null;
    public ?bool $hasActiveLeadObjectSegment = null;

    public function __construct(?Lead $lead, ?int $userId)
    {
        if ($lead) {
            $this->has_owner = $lead->hasOwner();
            $this->is_owner = $lead->isOwner($userId);
            $this->statusId = $lead->status;
            $this->projectId = $lead->project_id;
            $this->hasActiveLeadObjectSegment = (new LeadTaskListService($lead))->hasActiveLeadObjectSegment();
        }
    }
}
