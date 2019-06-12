<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[LeadChecklistType]].
 *
 * @see LeadChecklistType
 */
class LeadChecklistTypeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return LeadChecklistType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return LeadChecklistType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
