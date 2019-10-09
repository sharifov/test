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
            $userName = $user->username ?: $user->id;
            $project = $lead->project ? $lead->project->name : $lead->project_id;
            throw new \DomainException('User: ' . $userName . ' cant access to Project: ' . $project);
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
