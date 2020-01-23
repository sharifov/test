<?php

namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * Class UserDepartmentQuery
 */
class UserDepartmentQuery extends ActiveQuery
{

    /**
     * @param $depId
     * @return UserDepartmentQuery
     */
    public function usersByDep($depId): self
    {
        return $this->select('ud_user_id')->andWhere(['ud_dep_id' => $depId]);
    }

    /**
     * @param int $userId
     * @return UserDepartmentQuery
     */
    public function depsByUser(int $userId): self
    {
        return $this->select('ud_dep_id')->andWhere(['ud_user_id' => $userId]);
    }
}
