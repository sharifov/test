<?php

namespace modules\requestControl\models;

/**
 * This is the ActiveQuery class for [[UserSiteActivity]].
 *
 * @see UserSiteActivity
 */
class UserSiteActivityQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserSiteActivity[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserSiteActivity|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
