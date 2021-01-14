<?php

namespace modules\fileStorage\src\entity\fileLead;

/**
* @see FileLead
*/
class Scopes extends \yii\db\ActiveQuery
{
    public function byLead(int $leadId): self
    {
        return $this->andWhere(['fld_lead_id' => $leadId]);
    }

    /**
    * @return FileLead[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FileLead|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
