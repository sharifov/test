<?php

namespace sales\model\user\entity\payment;

/**
 * This is the ActiveQuery class for [[UserPayment]].
 *
 * @see UserPayment
 */
class UserPaymentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserPayment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserPayment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
