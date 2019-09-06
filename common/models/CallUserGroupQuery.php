<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[CallUserGroup]].
 *
 * @see CallUserGroup
 */
class CallUserGroupQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CallUserGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CallUserGroup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
