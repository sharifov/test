<?php

namespace common\models;

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
        return $this->select('ud_user_id')->distinct('ud_user_id')->andWhere(['ud_dep_id' => $depId]);
    }

    /**
     * @param $userId
     * @return UserDepartmentQuery
     */
    public function depsByUser($userId): self
    {
        return $this->select('ud_dep_id')->distinct('ud_dep_id')->andWhere(['ud_user_id' => $userId]);
    }

}
