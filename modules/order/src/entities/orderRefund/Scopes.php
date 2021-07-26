<?php

namespace modules\order\src\entities\orderRefund;

/**
 * This is the ActiveQuery class for [[OrderRefund]].
 *
 * @see OrderRefund
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return OrderRefund[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return OrderRefund|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
