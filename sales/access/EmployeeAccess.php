<?php

namespace sales\access;

use common\models\Employee;
use common\models\Lead;

class EmployeeAccess
{

    /**
     * @param Lead $lead
     * @param Employee $user
     */
    public static function leadAccess(Lead $lead, Employee $user): void
    {
        if (!array_key_exists($lead->project_id, EmployeeProjectAccess::getProjects($user))) {
            throw new \DomainException('User: ' . $user->id . ' cant access to ProjectId: ' . $lead->project_id);
        }
    }

    /**
     * @param Lead $lead
     * @param Employee $user
     */
    public static function caseAccess(Lead $lead, Employee $user): void
    {

    }

}
