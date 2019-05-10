<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[LeadCallExpert]].
 *
 * @see LeadCallExpert
 */
class LeadCallExpertQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return LeadCallExpert[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return LeadCallExpert|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
