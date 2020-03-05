<?php

namespace modules\order\src\entities\orderTipsUserProfit;

/**
 * This is the ActiveQuery class for [[OrderTipsUserProfit]].
 *
 * @see OrderTipsUserProfit
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return OrderTipsUserProfit[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return OrderTipsUserProfit|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
