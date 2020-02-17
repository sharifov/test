<?php

namespace modules\order\src\entities\orderUserProfit;

/**
 * This is the ActiveQuery class for [[OrderUserProfit]].
 *
 * @see OrderUserProfit
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return OrderUserProfit[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return OrderUserProfit|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
