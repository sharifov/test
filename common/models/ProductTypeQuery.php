<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ProductType]].
 *
 * @see ProductType
 */
class ProductTypeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ProductType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ProductType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
