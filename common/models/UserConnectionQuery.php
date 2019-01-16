<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserConnection]].
 *
 * @see UserConnection
 */
class UserConnectionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserConnection[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserConnection|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
