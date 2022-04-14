<?php

namespace src\model\leadPoorProcessingData\abac\dto;

use common\models\Lead;
use src\access\EmployeeDepartmentAccess;
use src\access\EmployeeProjectAccess;

/**
 * Class LeadPoorProcessingAbacDto
 */
class LeadPoorProcessingAbacDto extends \stdClass
{
    public ?int $leadStatusId = null;
    public ?int $leadProjectId = null;
    public ?int $leadTypeId = null;
    public ?int $leadCallStatusId = null;
    public ?bool $leadIsTest = null;
    public ?string $leadCabin = null;
    public ?string $leadCreated = null;
    public ?bool $leadIsClone = null;
    public ?bool $leadHasFlightDetails = null;


    public bool $isInProject = false;
    public bool $isInDepartment = false;


    /**
     * @param Lead|null $lead
     * @param int $userId
     */
    public function __construct(
        ?Lead $lead,
        int $userId
    ) {
        $this->leadStatusId = $lead->status ?? null;
        $this->leadProjectId = $lead->project_id ?? null;
        $this->leadTypeId =  $lead->l_type ?? null;
        $this->leadCallStatusId = $lead->l_call_status_id ?? null;
        $this->leadIsTest = $lead->l_is_test ?? null;
        $this->leadCabin = $lead->cabin ?? null;
        $this->leadCreated = $lead->created ?? null;
        $this->leadIsClone = $lead ? $lead->isCloneCreated() : null;
        $this->leadHasFlightDetails = $lead ? $lead->hasFlightDetails() : null;

        if ($lead && $lead->project_id) {
            $this->isInProject = EmployeeProjectAccess::isInProject($lead->project_id, $userId);
        }

        if ($lead && $lead->l_dep_id) {
            $this->isInDepartment = EmployeeDepartmentAccess::isInDepartment($lead->l_dep_id, $userId);
        }
    }
}
