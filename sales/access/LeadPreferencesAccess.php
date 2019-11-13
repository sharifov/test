<?php

namespace sales\access;

use common\models\Lead;
use Yii;

class LeadPreferencesAccess
{
	/**
	 * @param Lead $lead
	 * @param int $userId
	 * @return bool
	 */
	public static function isUserCanManageLeadPreference(Lead $lead, int $userId): bool
	{
		return (
			$lead->isOwner($userId) ||
			!Yii::$app->user->identity->isSimpleAgent() ||
			(Yii::$app->user->identity->isSupervision() && $lead->isGetOwner() &&
				EmployeeGroupAccess::isUserInCommonGroup($userId, $lead->employee_id))
		);
	}
}