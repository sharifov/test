<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserProjectParams]].
 *
 * @see UserProjectParams
 */
class UserProjectParamsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserProjectParams[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserProjectParams|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
