<?php

namespace sales\model\user\profit;

/**
 * This is the ActiveQuery class for [[UserProfit]].
 *
 * @see UserProfit
 */
class UserProfitQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserProfit[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserProfit|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
