<?php

namespace sales\model\clientChat\componentRule\entity;

/**
* @see ClientChatComponentRule
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return ClientChatComponentRule[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ClientChatComponentRule|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
