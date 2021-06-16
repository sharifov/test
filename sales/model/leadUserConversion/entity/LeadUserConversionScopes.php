<?php

namespace sales\model\leadUserConversion\entity;

/**
* @see LeadUserConversion
*/
class LeadUserConversionScopes extends \yii\db\ActiveQuery
{
    /**
    * @return LeadUserConversion[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return LeadUserConversion|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
