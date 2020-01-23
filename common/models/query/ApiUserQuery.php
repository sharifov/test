<?php

namespace common\models\query;

use common\models\ApiUser;

/**
 * This is the ActiveQuery class for [[ApiUser]].
 *
 * @see ApiUser
 */
class ApiUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ApiUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ApiUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
