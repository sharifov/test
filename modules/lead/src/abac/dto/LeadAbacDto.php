<?php

namespace modules\lead\src\abac\dto;

use common\models\Lead;
use common\models\Employee;
use sales\access\EmployeeGroupAccess;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeProjectAccess;
use yii\helpers\VarDumper;

/**
 * @property bool $is_owner
 * @property bool $has_owner
 * @property bool $has_owner_query
 * @property bool $is_common_group
 * @property bool $isShiftTime
 * @property bool $withinPersonalTakeLimits
 * @property bool $hasAppliedQuote
 * @property int|null $status_id
 */
class LeadAbacDto extends \stdClass
{
    public bool $is_owner;
    public bool $has_owner;
    public bool $has_owner_query;
    public bool $is_common_group = false;
    public ?int $status_id = null;
    public bool $isShiftTime;
    public bool $withinPersonalTakeLimits;
    public bool $hasAppliedQuote;
    public bool $isInProject;
    public bool $isInDepartment;


    /**
     * @param Lead|null $lead
     * @param int $userId
     * @throws \Exception
     */
    public function __construct(?Lead $lead, int $userId)
    {
        if ($lead) {
            $this->is_owner = $lead->isOwner($userId);
            $this->has_owner = $lead->hasOwner();
            $this->has_owner_query = $this->has_owner;

            if ($this->has_owner) {
                $this->is_common_group = EmployeeGroupAccess::isUserInCommonGroup($userId, $lead->employee_id);
            }

            if ($this->has_owner) {
                $this->isInProject = EmployeeProjectAccess::isInProject($lead->project_id, $userId);
                $this->isInDepartment = EmployeeDepartmentAccess::isInDepartment($lead->l_dep_id, $userId);
            }

            $user = Employee::findOne(['id' => $userId]);
            if ($user) {
                $this->hasAppliedQuote = $lead->hasAppliedQuote();
                $this->isShiftTime = $user->checkShiftTime();
                $this->withinPersonalTakeLimits = $user->accessTakeNewLead();
            }

            $this->status_id = $lead->status;
        }
    }
}
