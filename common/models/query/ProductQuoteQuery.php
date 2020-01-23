<?php

namespace common\models\query;

use common\models\ProductQuote;

/**
 * This is the ActiveQuery class for [[ProductQuote]].
 *
 * @see ProductQuote
 */
class ProductQuoteQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ProductQuote[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ProductQuote|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
