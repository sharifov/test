<?php

namespace sales\model\leadProduct\entity;

/**
* @see LeadProduct
*/
class Scopes extends \yii\db\ActiveQuery
{
    public function byLead(int $leadId): self
    {
        return $this->andWhere(['lp_lead_id' => $leadId]);
    }

    /**
    * @return LeadProduct[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return LeadProduct|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
