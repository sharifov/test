<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[DepartmentPhoneProject]].
 *
 * @see DepartmentPhoneProject
 */
class DepartmentPhoneProjectQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return DepartmentPhoneProject[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return DepartmentPhoneProject|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
