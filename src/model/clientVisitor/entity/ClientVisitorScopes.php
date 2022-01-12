<?php

namespace src\model\clientVisitor\entity;

/**
* @see ClientVisitor
*/
class ClientVisitorScopes extends \yii\db\ActiveQuery
{
    /**
    * @return ClientVisitor[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ClientVisitor|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
