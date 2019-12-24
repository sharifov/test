<?php

namespace common\models\query;

use common\models\OrderProduct;

/**
 * This is the ActiveQuery class for [[OrderProduct]].
 *
 * @see OrderProduct
 */
class OrderProductQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return OrderProduct[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return OrderProduct|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
