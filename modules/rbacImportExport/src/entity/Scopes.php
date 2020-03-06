<?php

namespace modules\rbacImportExport\src\entity;

/**
 * This is the ActiveQuery class for [[AuthImportExport]].
 *
 * @see AuthImportExport
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AuthImportExport[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AuthImportExport|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
