<?php

namespace modules\product\src\entities\productQuoteData;

/**
 * This is the ActiveQuery class for [[ProductQuoteData]].
 *
 * @see ProductQuoteData
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ProductQuoteData[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ProductQuoteData|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
