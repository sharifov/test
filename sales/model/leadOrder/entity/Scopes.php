<?php

namespace sales\model\leadOrder\entity;

/**
* @see LeadOrder
*/
class Scopes extends \yii\db\ActiveQuery
{
    public function byLead(int $leadId): self
    {
        return $this->andWhere(['lo_lead_id' => $leadId]);
    }

    /**
    * @return LeadOrder[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return LeadOrder|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
