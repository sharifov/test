<?php

namespace common\models\query;

use common\models\LeadChecklist;

/**
 * This is the ActiveQuery class for [[LeadChecklist]].
 *
 * @see LeadChecklist
 */
class LeadChecklistQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return LeadChecklist[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return LeadChecklist|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
