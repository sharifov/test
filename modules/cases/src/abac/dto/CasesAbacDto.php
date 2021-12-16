<?php

namespace modules\cases\src\abac\dto;

use sales\access\EmployeeGroupAccess;
use sales\auth\Auth;
use sales\entities\cases\Cases;

/**
 * @property bool $is_owner
 */
class CasesAbacDto extends \stdClass
{
    public bool $is_owner;
    public ?int $status_id = null;
    public ?int $category_id = null;
    public ?int $to_status = null;
    public bool $is_common_group = false;
    public ?int $pqc_status = null;
    public ?int $pqr_status = null;

    public function __construct(?Cases $case, ?int $statusSet = null)
    {
        if ($case) {
            $this->is_owner = $case->isOwner(Auth::id());
            $this->status_id = $case->cs_status;
            $this->category_id = $case->cs_category_id;

            if ($statusSet) {
                $this->to_status = $statusSet;
            }

            if ($case->hasOwner()) {
                $this->is_common_group = EmployeeGroupAccess::isUserInCommonGroup(Auth::id(), $case->cs_user_id);
            }
        }
    }
}
