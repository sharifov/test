<?php

namespace src\access;

use common\models\UserGroupAssign;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class EmployeeGroupAccess
{
    /**
     * @param int $userId
     * @param int $cacheDuration // Cache disable = "-1"
     * @return ActiveQuery
     *
     * Ex.
     * $cases = Cases::find()->andWhere(['cs_user_id' => EmployeeGroupAccess::usersIdsInCommonGroupsSubQuery($userId)])->all();
     */
    public static function usersIdsInCommonGroupsSubQuery(int $userId, int $cacheDuration = -1): ActiveQuery
    {
        return UserGroupAssign::find()
            ->select('related_users.ugs_user_id')
            ->innerJoin(
                UserGroupAssign::tableName() . ' AS related_users',
                UserGroupAssign::tableName() . '.ugs_group_id = related_users.ugs_group_id'
            )
            ->where([UserGroupAssign::tableName() . '.ugs_user_id' => $userId])
            ->groupBy('related_users.ugs_user_id')
            ->cache($cacheDuration);
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
     * @param null|int $searchUserId
     * @param int $userIdExist
     * @return bool
     */
    public static function isUserInCommonGroup(?int $searchUserId, int $userIdExist): bool
    {
        if (is_null($searchUserId)) {
            return false;
        }
        return array_key_exists($userIdExist, self::getUsersIdsInCommonGroups($searchUserId));
    }
}
