<?php

namespace modules\lead\src\abac\dto;

use common\models\Lead;
use sales\access\EmployeeGroupAccess;

/**
 * @property bool $is_owner
 * @property bool $has_owner
 * @property bool $is_common_group
 * @property int|null $status_id
 */
class LeadAbacDto extends \stdClass
{
    public bool $is_owner;
    public bool $has_owner;
    public bool $is_common_group;
    public ?int $status_id = null;

    /**
     * @param Lead|null $lead
     * @param int $userId
     */
    public function __construct(?Lead $lead, int $userId)
    {
        if ($lead) {
            $this->is_owner = $lead->isOwner($userId);
            $this->has_owner = $lead->hasOwner();
            $this->is_common_group = EmployeeGroupAccess::isUserInCommonGroup($userId, $lead->employee_id);
            $this->status_id = $lead->status;
        }
    }
}
