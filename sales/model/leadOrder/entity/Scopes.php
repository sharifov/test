<?php

namespace sales\model\leadOrder\entity;

/**
* @see LeadOrder
*/
class Scopes extends \yii\db\ActiveQuery
{
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
