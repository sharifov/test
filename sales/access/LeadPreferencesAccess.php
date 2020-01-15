<?php

namespace sales\access;

use common\models\Employee;
use common\models\Lead;

class LeadPreferencesAccess
{
	/**
	 * @param Lead $lead
	 * @param Employee $user
	 * @return bool
	 */
	public static function isUserCanManageLeadPreference(Lead $lead, Employee $user): bool
	{
		return (
			$lead->isOwner($user->id) ||
			!$user->isSimpleAgent() ||
			($user->isSupervision() && $lead->isGetOwner() &&
				EmployeeGroupAccess::isUserInCommonGroup($user->id, $lead->employee_id))
		);
	}
}
