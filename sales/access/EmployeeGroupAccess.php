<?php

namespace sales\access;

use common\models\UserGroupAssign;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class EmployeeGroupAccess
{

    /**
     * @param int $userId
     * @return ActiveQuery
     *
     * Ex.
     * $cases = Cases::find()->andWhere(['cs_user_id' => EmployeeGroupAccess::usersIdsInCommonGroupsSubQuery($userId)])->all();
     */
    public static function usersIdsInCommonGroupsSubQuery(int $userId): ActiveQuery
    {
        return UserGroupAssign::find()->select('ugs_user_id')->distinct()->andWhere([
            'ugs_group_id' => UserGroupAssign::find()->select(['ugs_group_id'])->andWhere(['ugs_user_id' => $userId])
        ]);
    }

    /**
     * @param int $userId
     * @return array
     *
     *   [
     *        2 => '2',
     *        6 => '6',
     *        23 => '23'
     *   ]
     */
    public static function getUsersIdsInCommonGroups(int $userId): array
    {
        return ArrayHelper::getColumn(self::usersIdsInCommonGroupsSubQuery($userId)->asArray()->indexBy('ugs_user_id')->asArray()->all(), 'ugs_user_id');
    }

	/**
	 * @param int $searchUserId
	 * @param int $userIdExist
	 * @return bool
	 */
    public static function isUserInCommonGroup(int $searchUserId, int $userIdExist): bool
	{
		return array_key_exists($userIdExist, self::getUsersIdsInCommonGroups($searchUserId));
	}
}
