<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\CreditCard]].
 *
 * @see \common\models\CreditCard
 */
class CreditCardQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\CreditCard[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\CreditCard|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
