<?php

namespace modules\lead\src\abac\queue;

use common\models\Lead;
use src\access\EmployeeDepartmentAccess;
use src\access\EmployeeProjectAccess;

class LeadBusinessExtraQueueAbacDto extends \stdClass
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
    public ?string $department_name = null;

    /**
     * @param Lead|null $lead
     */
    public function __construct(
        ?Lead $lead,
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
        $this->department_name = $lead ? $lead->lDep->dep_name : null;
    }
}
