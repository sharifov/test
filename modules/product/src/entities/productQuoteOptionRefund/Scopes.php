<?php

namespace modules\product\src\entities\productQuoteOptionRefund;

/**
 * This is the ActiveQuery class for [[ProductQuoteOptionRefund]].
 *
 * @see ProductQuoteOptionRefund
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ProductQuoteOptionRefund[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ProductQuoteOptionRefund|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
