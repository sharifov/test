<?php

namespace sales\guards\lead;

use common\models\Employee;
use common\models\Lead;
use sales\access\EmployeeGroupAccess;

class EditGuard
{

    /**
     * @param Lead $lead
     * @param Employee $user
     */
    public function guard(Lead $lead, Employee $user): void
    {
        if ($user->isAdmin()) {
            return;
        }
        if ($lead->isOwner($user->id)) {
            return;
        }
        if ($user->isAgent()) {
            throw new \DomainException('Cant access for edit Lead');
        }
        if ($user->isSupervision()) {
            $commonUsersIds = Employee::find()
                ->select(['id', 'status'])->active()
                ->andWhere(['id' => EmployeeGroupAccess::usersIdsInCommonGroupsSubQuery($user->id)])
                ->asArray()->indexBy('id')->column();
            if (!array_key_exists($lead->employee_id, $commonUsersIds)) {
                throw new \DomainException('Cant access for edit Lead');
            }
        }
    }

}
