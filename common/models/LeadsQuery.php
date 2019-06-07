<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Lead2]].
 *
 * @see Lead2
 */
class LeadsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Lead2[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Lead2|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
