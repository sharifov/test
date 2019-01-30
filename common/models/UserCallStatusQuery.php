<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserCallStatus]].
 *
 * @see UserCallStatus
 */
class UserCallStatusQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserCallStatus[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserCallStatus|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
