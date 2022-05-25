<?php

namespace modules\cases\src\abac\saleSearch;

use src\access\EmployeeGroupAccess;
use src\auth\Auth;
use src\entities\cases\Cases;

/**
 * Class CaseSaleSearchAbacDto
 */
class CaseSaleSearchAbacDto extends \stdClass
{
    public bool $is_owner;
    public bool $has_owner;
    public int $status_id;
    public string $project_name;

    public function __construct(Cases $case, int $userId)
    {
        $this->is_owner = $case->isOwner($userId);
        $this->has_owner = $case->hasOwner();
        $this->status_id = $case->cs_status;
        $this->project_name = $case->project->name ?? '';
    }
}
