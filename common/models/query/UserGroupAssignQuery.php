<?php

namespace common\models\query;

use common\models\Employee;
use common\models\UserGroup;
use common\models\UserGroupAssign;

/**
 * This is the ActiveQuery class for [[UserGroupAssign]].
 *
 * @see UserGroupAssign
 */
class UserGroupAssignQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserGroupAssign[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserGroupAssign|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param array $groupIds
     * @return UserGroupAssign[]
     */
    public static function getGroupedDataByGroups(array $groupIds): array
    {
        return UserGroupAssign::find()
            ->select(['userId' => 'ugs_user_id', 'username', 'groupName' => 'ug_name'])
//            ->joinWith(['ugsUser', 'ugsGroup'])
            ->innerJoin(Employee::tableName(), 'ugs_user_id = id')
            ->innerJoin(UserGroup::tableName(), 'ugs_group_id = ug_id')
            ->andWhere(['ugs_group_id' => $groupIds])
            ->orderBy(['groupName' => SORT_ASC, 'username' => SORT_ASC])
//            ->groupBy(['ugs_user_id'])
            ->asArray()
            ->all();
    }
}
