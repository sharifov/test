<?php

namespace sales\access;

use common\models\Department;
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
        $list = new ListsAccess($user->id);
        if (!array_key_exists($lead->project_id, $list->getProjects())) {
            throw new \DomainException('User: ' . $user->id . ' cant access to ProjectId: ' . $lead->project_id);
        }
        $leadDepartment = $lead->l_dep_id ?: Department::DEPARTMENT_SALES;
        if (!array_key_exists($leadDepartment, $list->getDepartments())) {
            throw new \DomainException('User: ' . $user->id . ' cant access to DepartmentId: ' . $lead->l_dep_id);
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
