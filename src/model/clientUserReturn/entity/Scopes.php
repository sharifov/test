<?php

namespace src\model\clientUserReturn\entity;

/**
 * This is the ActiveQuery class for [[ClientUserReturn]].
 *
 * @see ClientUserReturn
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ClientUserReturn[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ClientUserReturn|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
