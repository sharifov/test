<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[UserFailedLogin]].
 *
 * @see UserFailedLogin
 */
class UserFailedLoginQuery extends \yii\db\ActiveQuery
{
    /**
     * @param string $limitDateTime
     * @return UserFailedLoginQuery
     */
    public function byLimitDateTime(string $limitDateTime): UserFailedLoginQuery
    {
        return $this->andWhere(['>=', 'ufl_created_dt', $limitDateTime]);
    }

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
