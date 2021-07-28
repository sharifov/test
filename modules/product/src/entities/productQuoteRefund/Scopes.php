<?php

namespace modules\product\src\entities\productQuoteRefund;

/**
 * This is the ActiveQuery class for [[ProductQuoteRefund]].
 *
 * @see ProductQuoteRefund
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ProductQuoteRefund[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ProductQuoteRefund|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
