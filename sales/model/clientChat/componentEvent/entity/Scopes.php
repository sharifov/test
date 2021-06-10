<?php

namespace sales\model\clientChat\componentEvent\entity;

/**
* @see ClientChatComponentEvent
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return ClientChatComponentEvent[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ClientChatComponentEvent|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
