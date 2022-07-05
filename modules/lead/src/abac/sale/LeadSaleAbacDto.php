<?php

namespace modules\lead\src\abac\sale;

use common\models\Lead;
use src\access\EmployeeDepartmentAccess;
use src\access\EmployeeProjectAccess;

class LeadSaleAbacDto extends \stdClass
{
    public bool $is_owner;
    public bool $has_owner;
    public bool $isInProject = false;
    public bool $isInDepartment = false;
    public ?int $status_id = null;

    /**
     * @param Lead $lead
     * @param int $userId
     */
    public function __construct(Lead $lead, int $userId)
    {
        $this->is_owner = $lead->isOwner($userId);
        $this->has_owner = $lead->hasOwner();

        if ($lead->project_id) {
            $this->isInProject = EmployeeProjectAccess::isInProject($lead->project_id, $userId);
        }

        if ($lead->l_dep_id) {
            $this->isInDepartment = EmployeeDepartmentAccess::isInDepartment($lead->l_dep_id, $userId);
        }
        $this->status_id = $lead->status;
    }
}
