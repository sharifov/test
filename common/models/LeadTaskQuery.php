<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[LeadTask]].
 *
 * @see LeadTask
 */
class LeadTaskQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return LeadTask[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return LeadTask|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
