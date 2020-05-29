<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[UserFailedLogin]].
 *
 * @see UserFailedLogin
 */
class UserFailedLoginQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserFailedLogin[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserFailedLogin|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
