<?php

namespace src\model\leadDataKey\entity;

/**
* @see LeadDataKey
*/
class LeadDataKeyScopes extends \yii\db\ActiveQuery
{
    /**
    * @return LeadDataKey[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return LeadDataKey|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
