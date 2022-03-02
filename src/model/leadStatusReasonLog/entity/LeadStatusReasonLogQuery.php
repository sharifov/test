<?php

namespace src\model\leadStatusReasonLog\entity;

/**
 * This is the ActiveQuery class for [[LeadStatusReasonLog]].
 *
 * @see LeadStatusReasonLog
 */
class LeadStatusReasonLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return LeadStatusReasonLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return LeadStatusReasonLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
