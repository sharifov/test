<?php

namespace common\models\query;

use common\models\UserGroup;
use common\models\UserGroupAssign;
use yii\helpers\ArrayHelper;

/**
 * Class UserGroupQuery
 *
 * @see UserGroup
 */
class UserGroupQuery extends \yii\db\ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['ug_disable' => 0]);
    }

    public static function getList(): array
    {
        $data = UserGroup::find()
            ->enabled()
            ->orderBy(['ug_name' => SORT_ASC])
            ->asArray()
            ->all();
        return ArrayHelper::map($data, 'ug_id', 'ug_name');
    }

    public static function getListByUser(int $id): array
    {
        $data = UserGroup::find()
            ->join('inner join', UserGroupAssign::tableName(), 'ug_id = ugs_group_id')
            ->enabled()
            ->andWhere(['ugs_user_id' => $id])
            ->orderBy(['ug_name' => SORT_ASC])
            ->asArray()
            ->all();
        return ArrayHelper::map($data, 'ug_id', 'ug_name');
    }

    /**
     * @param int $userId
     * @param array $groupIds
     * @return UserGroup[]
     */
    public static function findUserGroups(?int $userId, array $groupIds = []): array
    {
        $query = UserGroup::find()
            ->join('inner join', UserGroupAssign::tableName(), 'ug_id = ugs_group_id')
            ->enabled()
            ->orderBy(['ug_name' => SORT_ASC]);
        if ($userId) {
            $query->andWhere(['ugs_user_id' => $userId]);
        }
        if ($groupIds) {
            $query->andWhere(['ug_id' => $groupIds]);
        }
        return $query->all();
    }
}
