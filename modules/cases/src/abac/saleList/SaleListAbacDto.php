<?php

namespace modules\cases\src\abac\saleList;

use src\access\EmployeeGroupAccess;
use src\auth\Auth;
use src\entities\cases\Cases;

class SaleListAbacDto extends \stdClass
{
    public bool $is_owner;
    public bool $is_common_group_owner;
    public bool $has_owner;
    public int $status_id;
    public string $department_name;
    public ?int $category_id;
    public string $project_name;
    public bool $is_automate;
    public bool $need_action;
    public bool $is_common_group;

    public function __construct(Cases $case, int $userId)
    {
        $this->is_owner = $case->isOwner($userId);
        $this->has_owner = $case->hasOwner();
        $this->status_id = $case->cs_status;
        $this->department_name = $case->department->dep_name ?? '';
        $this->category_id = $case->cs_category_id ?: null;
        $this->project_name = $case->project->name ?? '';
        $this->is_automate = $case->isAutomate();
        $this->need_action = $case->isNeedAction();

        if ($case->hasOwner()) {
            $this->is_common_group = EmployeeGroupAccess::isUserInCommonGroup($userId, $case->cs_user_id);
        }
    }
}
