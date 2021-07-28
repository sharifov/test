<?php

namespace modules\product\src\entities\productQuoteObjectRefund;

/**
 * This is the ActiveQuery class for [[ProductQuoteObjectRefund]].
 *
 * @see ProductQuoteObjectRefund
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ProductQuoteObjectRefund[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ProductQuoteObjectRefund|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
