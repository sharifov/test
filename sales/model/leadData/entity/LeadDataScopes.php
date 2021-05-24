<?php

namespace sales\model\leadData\entity;

/**
* @see LeadData
*/
class LeadDataScopes extends \yii\db\ActiveQuery
{
    /**
    * @return LeadData[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return LeadData|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
