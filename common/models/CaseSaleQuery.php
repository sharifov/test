<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[CaseSale]].
 *
 * @see CaseSale
 */
class CaseSaleQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CaseSale[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CaseSale|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
