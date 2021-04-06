<?php

namespace sales\model\caseOrder\entity;

/**
* @see CaseOrder
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return CaseOrder[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return CaseOrder|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
