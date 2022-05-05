<?php

namespace modules\lead\src\abac\queue;

use common\models\Lead;
use src\access\EmployeeDepartmentAccess;
use src\access\EmployeeProjectAccess;

/**
 * Class LeadQueueBusinessInboxAbacDto
 */
class LeadQueueBusinessInboxAbacDto extends \stdClass
{
    public ?bool $is_owner = null;
    public ?bool $has_owner = null;
    public ?int $status_id = null;
    public ?int $project_id = null;
    public ?bool $isInProject = null;
    public ?bool $isInDepartment = null;

    public function __construct(?Lead $lead, ?int $userId)
    {
        if ($lead) {
            $this->is_owner = $lead->isOwner($userId);
            $this->has_owner = $lead->hasOwner();
            $this->status_id = $lead->status;
            $this->project_id = $lead->project_id;

            if ($lead->project_id) {
                $this->isInProject = EmployeeProjectAccess::isInProject($lead->project_id, $userId);
            }

            if ($lead->l_dep_id) {
                $this->isInDepartment = EmployeeDepartmentAccess::isInDepartment($lead->l_dep_id, $userId);
            }
        }
    }
}
