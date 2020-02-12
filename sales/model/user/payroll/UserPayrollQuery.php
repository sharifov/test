<?php

namespace sales\model\user\payroll;

/**
 * This is the ActiveQuery class for [[UserPayroll]].
 *
 * @see UserPayroll
 */
class UserPayrollQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserPayroll[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserPayroll|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
