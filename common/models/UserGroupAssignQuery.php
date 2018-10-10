<?php

namespace common\models;

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
}
