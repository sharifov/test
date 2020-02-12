<?php

namespace sales\model\user\paymentCategory;

/**
 * This is the ActiveQuery class for [[UserPaymentCategory]].
 *
 * @see UserPaymentCategory
 */
class UserPaymentCategoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserPaymentCategory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserPaymentCategory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
