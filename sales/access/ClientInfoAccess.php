<?php


namespace sales\access;

use Yii;
use common\models\Lead;

class ClientInfoAccess
{
	/**
	 * @param Lead $lead
	 * @param int $userId
	 * @return bool
	 */
	public static function isUserCanManageLeadClientInfo(Lead $lead, int $userId): bool
	{
		return (
			$lead->isOwner(Yii::$app->user->id) ||
			!Yii::$app->user->identity->isSimpleAgent() ||
			(Yii::$app->user->identity->isSupervision() && $lead->isGetOwner() &&
			EmployeeGroupAccess::isUserInCommonGroup(Yii::$app->user->id, $lead->employee_id))
		);
	}

}