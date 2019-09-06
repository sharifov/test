<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[DepartmentPhoneProjectUserGroup]].
 *
 * @see DepartmentPhoneProjectUserGroup
 */
class DepartmentPhoneProjectUserGroupQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return DepartmentPhoneProjectUserGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return DepartmentPhoneProjectUserGroup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
